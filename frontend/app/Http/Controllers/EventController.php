<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class EventController extends Controller
{
    public function show($id)
    {
        $client = new Client();

        try {
            $response = $client->get("http://localhost:5000/api/event/{$id}");
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['success']) && $data['success'] === true) {
                return view('event-detail', ['event' => $data]);
            }

            return view('event-not-found');
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody()->getContents(), true);
                // return view('event-error', ['error' => $error['message'] ?? 'Unknown error']);
            }
            // return view('event-error', ['error' => 'Could not connect to the event service']);
        }
    }
}
