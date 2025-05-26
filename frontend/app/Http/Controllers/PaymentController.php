<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function registerAndPay(Request $request, $eventId)
    {
        $client = new Client();
        $userId = $request->session()->get('userId'); // Fixed session key

        // Handle file upload
        if (!$request->hasFile('proof_url')) {
            return redirect()->back()->with('error', 'Proof of payment is required.');
        }

        try {
            // Upload file to storage (or to your Node server)
            $file = $request->file('proof_url');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('proofs', $fileName, 'public');

            $response = $client->post("http://localhost:3000/api/register-and-pay", [
                'json' => [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'proof_url' => asset('storage/' . $filePath),
                    'amount' => $request->input('amount', 100), // Default or from event
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return redirect()->back()->with('success', $data['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process registration: ' . $e->getMessage());
        }
    }
}
