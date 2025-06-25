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

        try {
            $response = Http::get("http://localhost:5000/api/certificates/user/{$userId}");

            if ($response->failed()) {
                throw new \Exception('Failed to fetch certificates from API.');
            }

            $data = $response->json();
            $certificates = $data['data'] ?? [];

            return view('certificates.index', compact('certificates'));
        } catch (\Exception $e) {
            // Log the error and handle it (e.g., return an empty array or error view)
            Log::error('API Error: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Unable to load certificates. Please try again later.']);
        }
    }
}
