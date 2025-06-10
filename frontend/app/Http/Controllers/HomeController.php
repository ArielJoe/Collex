<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $apiBaseUrl = 'http://localhost:5000/api/event';

    public function index(Request $request)
    {
        $location = $request->query('location');
        $keyword = $request->query('keyword');
        $page = $request->query('page', 1);
        $faculty = $request->query('faculty'); // Changed from $facultyFilter to $faculty to match the template
        $date = $request->query('date');

        try {
            $queryParams = [
                'page' => $page,
                'limit' => 10
            ];

            if ($location) {
                $queryParams['location'] = $location;
            }
            if ($keyword) {
                $queryParams['search'] = $keyword;
            }
            if ($faculty) {
                $queryParams['faculty_id'] = $faculty; // Map to faculty_id as expected by the API
            }
            if ($date) {
                $queryParams['date'] = $date; // Add date filter if the API supports it
            }

            $response = Http::get($this->apiBaseUrl, $queryParams);

            if ($response->failed()) {
                Log::error('API request failed: Status ' . $response->status() . ' - Body: ' . $response->body());
                return view('index', [
                    'events' => [],
                    'totalPages' => 1,
                    'currentPage' => 1,
                    'locationFilter' => $location,
                    'keyword' => $keyword,
                    'faculty' => $faculty, // Changed from facultyFilter to faculty
                    'date' => $date,
                ]);
            }

            $data = $response->json();

            if (!is_array($data) || !isset($data['data'])) {
                Log::error('API response format error. Expected "data" key. Response: ', $data);
                return view('index', [
                    'events' => [],
                    'totalPages' => 1,
                    'currentPage' => 1,
                    'locationFilter' => $location,
                    'keyword' => $keyword,
                    'faculty' => $faculty, // Changed from facultyFilter to faculty
                    'date' => $date,
                ]);
            }

            return view('index', [
                'events' => $data['data'] ?? [],
                'totalPages' => $data['totalPages'] ?? 1,
                'currentPage' => $data['currentPage'] ?? 1,
                'locationFilter' => $location,
                'keyword' => $keyword,
                'faculty' => $faculty, // Changed from facultyFilter to faculty
                'date' => $date,
            ]);
        } catch (\Exception $e) {
            Log::error('API request exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return view('index', [
                'events' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'locationFilter' => $location,
                'keyword' => $keyword,
                'faculty' => $faculty, // Changed from facultyFilter to faculty
                'date' => $date,
            ]);
        }
    }
}
