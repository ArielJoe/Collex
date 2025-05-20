<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:5000/api/event';

    public function index(Request $request)
    {
        $location = $request->query('location');
        $page = $request->query('page', 1);

        try {
            $response = Http::get("{$this->apiBaseUrl}", [
                'location' => $location,
                'page' => $page,
                'limit' => 10
            ]);

            if ($response->failed()) {
                // \Log::error('API request failed: ' . $response->body());
                return view('index', [
                    'events' => [],
                    'totalPages' => 1,
                    'currentPage' => 1,
                    'locationFilter' => $location
                ]);
            }

            $data = $response->json();

            return view('index', [
                'events' => $data['events'] ?? [],
                'totalPages' => $data['totalPages'] ?? 1,
                'currentPage' => $data['currentPage'] ?? 1,
                'locationFilter' => $location
            ]);
        } catch (\Exception $e) {
            // \Log::error('API request exception: ' . $e->getMessage());
            return view('index', [
                'events' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'locationFilter' => $location
            ]);
        }
    }
}
