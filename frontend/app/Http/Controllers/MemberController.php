<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    public function index()
    {
        return view('member.index');
    }

    public function certificates(Request $request)
    {
        // Fetch certificates from the Node.js API for the authenticated user
        $userId = session()->get('userId');
        $certificates = [];

        try {
            $response = Http::get("http://localhost:5000/api/certificates/user/{$userId}");
            $data = $response->json();

            if ($response->successful() && isset($data['success']) && $data['success']) {
                $certificates = $data['data'] ?? [];
            } elseif ($response->status() === 404 || (isset($data['success']) && !$data['success'])) {
                // Handle case where no certificates are found (API returns 404 or success: false)
                $certificates = [];
            } else {
                throw new \Exception('Failed to fetch certificates from API: ' . ($data['message'] ?? 'Unknown error'));
            }

            return view('certificates.index', compact('certificates'));
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Unable to load certificates. Please try again later.']);
        }
    }
}
