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

        try {
            $nodeApiUrl = 'http://localhost:5000';
            $apiUrl = "{$nodeApiUrl}/api/registration/my-tickets/{$userId}";

            $response = Http::get($apiUrl);

            if ($response->successful() && $response->json('success')) {
                $tickets = $response->json('data');
                Log::info('Fetched tickets with QR codes and attendance status:', ['tickets' => $tickets]);
            } else {
                $error = $response->json('message') ?? 'Could not retrieve tickets at this time.';
                Log::error('Node API Error for my-tickets: ' . $response->body());
            }
        } catch (\Exception $e) {
            $error = 'A system error occurred while fetching your tickets.';
            Log::critical('Could not connect to Node API: ' . $e->getMessage());
        }

        return view('member.index', compact('tickets', 'error'));
    }
}
