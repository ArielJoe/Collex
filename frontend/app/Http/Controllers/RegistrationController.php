<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegistrationController extends Controller
{
    public function showMyTickets()
    {
        $userId = session()->get('userId');
        $tickets = [];
        $error = null;

        // Ensure the Laravel user is linked to a MongoDB user ID
        // if (empty($user->mongo_user_id)) {
        //     return view('tickets.my-tickets')->withErrors('Your account is not properly configured.');
        // }

        try {
            $nodeApiUrl = 'http://localhost:5000';

            // --- THIS IS THE CHANGE ---
            // Construct the new URL by adding the user's ID to the end.
            // No headers are needed anymore.
            $apiUrl = "{$nodeApiUrl}/api/registration/my-tickets/{$userId}";

            $response = Http::get($apiUrl);

            if ($response->successful() && $response->json('success')) {
                $tickets = $response->json('data');
            } else {
                $error = $response->json('message') ?? 'Could not retrieve tickets at this time.';
                Log::error('Node API Error for my-tickets: ' . $response->body());
            }
        } catch (\Exception $e) {
            $error = 'A system error occurred while fetching your tickets.';
            Log::critical('Could not connect to Node API: ' . $e->getMessage());
        }

        // Pass the fetched tickets (or an empty array) and any errors to the Blade view
        return view('member.index', compact('tickets', 'error'));
    }
}
