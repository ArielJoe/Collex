<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class EventController extends Controller
{
    public function show($id)
    {
        $client = new Client();
        $response = $client->get("http://localhost:5000/api/event/{$id}"); // Adjust URL to your Express server
        $event = json_decode($response->getBody()->getContents(), true);
        
        return view('event-detail', compact('event'));
    }
}
