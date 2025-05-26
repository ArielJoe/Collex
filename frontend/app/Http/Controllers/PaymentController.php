<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function registerAndPay(Request $request, $eventId)
    {
        $client = new Client();
        $userId = $request->session()->get('userId');

        // Validate that user is logged in
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Validate file upload
        $request->validate([
            'proof_url' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB max
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            // Handle file upload
            $file = $request->file('proof_url');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('payment-proofs', $fileName, 'public');
            $fullFileUrl = asset('storage/' . $filePath);

            Log::info('File uploaded successfully', [
                'file_path' => $filePath,
                'file_url' => $fullFileUrl,
                'user_id' => $userId,
                'event_id' => $eventId
            ]);

            // Make API call to Node.js backend
            $response = $client->post("http://localhost:5000/api/register-and-pay", [
                'json' => [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'proof_url' => $fullFileUrl,
                    'amount' => floatval($request->input('amount')),
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 30 // 30 seconds timeout
            ]);

            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody()->getContents(), true);

            Log::info('API response received', [
                'status_code' => $statusCode,
                'response_data' => $data
            ]);

            if ($statusCode >= 200 && $statusCode < 300) {
                // Success response
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $data['message'] ?? 'Registration successful!',
                        'data' => $data
                    ]);
                }

                return redirect()->back()->with('success', $data['message'] ?? 'Registration successful!');
            } else {
                throw new \Exception($data['message'] ?? 'Failed to process registration');
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = 'Failed to connect to registration service';

            if ($e->hasResponse()) {
                $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $errorResponse['message'] ?? $errorMessage;
            }

            Log::error('API request failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'event_id' => $eventId
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('General error in registerAndPay', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'event_id' => $eventId
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process registration: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to process registration: ' . $e->getMessage());
        }
    }

    /**
     * Check payment status for debugging
     */
    public function checkPaymentStatus(Request $request, $eventId)
    {
        $client = new Client();
        $userId = $request->session()->get('userId');

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        try {
            $response = $client->get("http://localhost:5000/api/check-payment-status", [
                'query' => [
                    'user_id' => $userId,
                    'event_id' => $eventId
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'event_id' => $eventId
            ]);

            return response()->json([
                'error' => 'Failed to check payment status',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
