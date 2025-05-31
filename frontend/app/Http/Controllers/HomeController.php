<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Tambahkan Log facade

class HomeController extends Controller
{
    // Pastikan ini adalah base URL yang benar untuk endpoint events Anda
    protected $apiBaseUrl = 'http://localhost:5000/api/event';

    public function index(Request $request)
    {
        $location = $request->query('location');
        $search = $request->query('search'); // Ambil parameter 'search'
        $page = $request->query('page', 1);
        $facultyFilter = $request->query('faculty'); // Untuk pagination link
        $dateFilter = $request->query('date'); // Untuk pagination link

        try {
            $queryParams = [
                'page' => $page,
                'limit' => 10 // Anda bisa membuat limit ini dinamis jika perlu
            ];

            if ($location) {
                $queryParams['location'] = $location; // API Node.js Anda perlu mendukung filter ini
            }
            if ($search) {
                $queryParams['search'] = $search; // API Node.js Anda mendukung filter 'search' untuk nama event
            }
            // Jika Anda ingin filter berdasarkan faculty_id dari sidebar, Anda perlu menambahkan:
            // if ($facultyFilter) {
            //     $queryParams['faculty_id'] = $facultyFilter;
            // }

            $response = Http::get($this->apiBaseUrl, $queryParams);

            if ($response->failed()) {
                Log::error('API request failed: Status ' . $response->status() . ' - Body: ' . $response->body());
                return view('index', [
                    'events' => [],
                    'totalPages' => 1,
                    'currentPage' => 1,
                    'locationFilter' => $location,
                    'searchFilter' => $search, // Teruskan filter pencarian ke view
                    'facultyFilter' => $facultyFilter,
                    'dateFilter' => $dateFilter
                ]);
            }

            $data = $response->json();

            // Pastikan $data adalah array dan memiliki key yang diharapkan
            if (!is_array($data) || !isset($data['data'])) {
                Log::error('API response format error. Expected "data" key. Response: ', $data);
                return view('index', [
                    'events' => [],
                    'totalPages' => 1,
                    'currentPage' => 1,
                    'locationFilter' => $location,
                    'searchFilter' => $search,
                    'facultyFilter' => $facultyFilter,
                    'dateFilter' => $dateFilter
                ]);
            }

            return view('index', [
                'events' => $data['data'] ?? [], // Mengakses events dari $data['data']
                'totalPages' => $data['totalPages'] ?? 1,
                'currentPage' => $data['currentPage'] ?? 1,
                'locationFilter' => $location,
                'searchFilter' => $search, // Teruskan filter pencarian ke view untuk pagination dan form
                'facultyFilter' => $facultyFilter,
                'dateFilter' => $dateFilter
            ]);
        } catch (\Exception $e) {
            Log::error('API request exception: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return view('index', [
                'events' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'locationFilter' => $location,
                'searchFilter' => $search,
                'facultyFilter' => $facultyFilter,
                'dateFilter' => $dateFilter
            ]);
        }
    }
}
