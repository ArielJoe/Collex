<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Penting untuk debugging
use Illuminate\Validation\Rule; // Untuk validasi enum

class AdminController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        // Menggunakan API_BASE_URL dari .env dan menambahkan /api/users
        // Pastikan API_BASE_URL di .env adalah 'http://localhost:5000'
        $this->apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/user';
    }

    // Dashboard with user stats
    public function dashboard()
    {
        try {
            // Endpoint di Node.js adalah GET /api/users/stats
            $response = Http::get("{$this->apiBaseUrl}/stats");

            if ($response->failed()) {
                Log::error('Admin Dashboard - API stats request failed: ' . $response->body());
                return view('admin.dashboard', ['stats' => null])->with('error', 'Gagal mengambil data statistik pengguna.');
            }

            $responseData = $response->json();
            // Asumsi API mengembalikan { success: true, data: statsObject }
            $stats = (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data'])) ? $responseData['data'] : null;

            return view('admin.dashboard', compact('stats'));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Admin Dashboard - API connection error: ' . $e->getMessage());
            return view('admin.dashboard', ['stats' => null])->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error('Admin Dashboard - Generic error: ' . $e->getMessage());
            return view('admin.dashboard', ['stats' => null])->with('error', 'Terjadi kesalahan saat mengambil data dashboard.');
        }
    }

    // List users
    public function index(Request $request)
    {
        $role = $request->query('role');
        $page = $request->query('page', 1);
        $search = $request->query('search'); // Tambahkan parameter pencarian

        try {
            $queryParams = [
                'page' => $page,
                'limit' => 10 // Anda bisa membuat ini konfigurabel
            ];
            if ($role) {
                $queryParams['role'] = $role;
            }
            if ($search) {
                $queryParams['search'] = $search;
            }

            // Endpoint di Node.js adalah GET /api/users
            $response = Http::get($this->apiBaseUrl, $queryParams);

            if ($response->failed()) {
                Log::error('Admin User Index - API request failed: ' . $response->body());
                return back()->with('error', 'Gagal mengambil daftar pengguna.');
            }

            $responseData = $response->json();
            // Asumsi API mengembalikan { success: true, data: users, totalPages, currentPage, totalUsers }
            if (isset($responseData['success']) && $responseData['success'] === true) {
                return view('admin.user.index', [
                    'users' => $responseData['data'] ?? [],
                    'totalPages' => $responseData['totalPages'] ?? 1,
                    'currentPage' => $responseData['currentPage'] ?? 1,
                    'totalUsers' => $responseData['totalUsers'] ?? 0,
                    'roleFilter' => $role,
                    'searchFilter' => $search
                ]);
            }

            return back()->with('error', $responseData['message'] ?? 'Gagal mengambil data pengguna.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Admin User Index - API connection error: ' . $e->getMessage());
            return back()->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error('Admin User Index - Generic error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil daftar pengguna.');
        }
    }

    // Show user details (jika ada halaman detail user di admin)
    public function show($id)
    {
        try {
            // Endpoint di Node.js adalah GET /api/users/:id
            $response = Http::get("{$this->apiBaseUrl}/{$id}");

            if ($response->failed()) {
                Log::error("Admin User Show - API request for user {$id} failed: " . $response->body());
                if ($response->status() == 404) {
                    return redirect()->route('admin.user.index')->with('error', 'Pengguna tidak ditemukan.');
                }
                return back()->with('error', 'Gagal mengambil detail pengguna.');
            }

            $responseData = $response->json();
            if (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data'])) {
                $user = $responseData['data'];
                return view('admin.user.show', compact('user'));
            }

            return redirect()->route('admin.user.index')->with('error', $responseData['message'] ?? 'Gagal mengambil detail pengguna.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Admin User Show - API connection error for user {$id}: " . $e->getMessage());
            return redirect()->route('admin.user.index')->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error("Admin User Show - Generic error for user {$id}: " . $e->getMessage());
            return redirect()->route('admin.user.index')->with('error', 'Terjadi kesalahan.');
        }
    }

    // Show create user form
    public function create()
    {
        return view('admin.user.create');
    }

    // Store new user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^\d{10,15}$/', // Sesuaikan regex jika perlu
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6', // Minimal 6 karakter untuk password baru
            'role' => ['required', Rule::in(['member', 'admin', 'finance', 'organizer'])],
            'photo_url' => 'nullable|url|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            // Endpoint di Node.js adalah POST /api/users
            $response = Http::post($this->apiBaseUrl, $validatedData);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('admin.user.index')->with('success', $responseData['message'] ?? 'Pengguna berhasil dibuat.');
            }

            Log::error('Admin User Store - API request failed: ' . $response->body());
            return back()->withInput()->with('error', $responseData['message'] ?? 'Gagal membuat pengguna.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Admin User Store - API connection error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error('Admin User Store - Generic error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat pengguna.');
        }
    }

    // Show edit user form
    public function edit($id)
    {

        try {
            // Endpoint di Node.js adalah GET /api/users/:id
            $response = Http::get("{$this->apiBaseUrl}/{$id}");

            if ($response->failed()) {
                Log::error("Admin User Edit - API request for user {$id} failed: " . $response->body());
                if ($response->status() == 404) {
                    return redirect()->route('admin.user.index')->with('error', 'Pengguna tidak ditemukan.');
                }
                return redirect()->route('admin.user.index')->with('error', 'Gagal mengambil data pengguna untuk diedit.');
            }

            $responseData = $response->json();
            if (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data'])) {
                $user = $responseData['data'];
                return view('admin.user.edit', compact('user'));
            }
            return redirect()->route('admin.user.index')->with('error', $responseData['message'] ?? 'Gagal mengambil data pengguna.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Admin User Edit - API connection error for user {$id}: " . $e->getMessage());
            return redirect()->route('admin.user.index')->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error("Admin User Edit - Generic error for user {$id}: " . $e->getMessage());
            return redirect()->route('admin.user.index')->with('error', 'Terjadi kesalahan.');
        }
    }

    // Update user
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'full_name' => 'nullable',
            'phone_number' => 'nullable',
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', Rule::in(['member', 'admin', 'finance', 'organizer'])],
            'photo_url' => 'nullable',
            'is_active' => 'nullable',
            'password' => 'nullable', // Password opsional, jika diisi harus dikonfirmasi
        ]);

        // Hanya kirim password jika diisi
        $payload = $validatedData;  
        if (empty($validatedData['password'])) {
            unset($payload['password']);
        }

        try {
            // Endpoint di Node.js adalah PUT /api/users/:id
            $response = Http::put("{$this->apiBaseUrl}/{$id}", $payload);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('admin.user.index')->with('success', $responseData['message'] ?? 'Pengguna berhasil diperbarui.');
            }

            Log::error("Admin User Update - API request for user {$id} failed: " . $response->body());
            return back()->withInput()->with('error', $responseData['message'] ?? 'Gagal memperbarui pengguna.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Admin User Update - API connection error for user {$id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error("Admin User Update - Generic error for user {$id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui pengguna.');
        }
    }

    // Delete user (soft delete)
    public function destroy($id)
    {
        try {
            // Endpoint di Node.js adalah DELETE /api/users/:id
            $response = Http::delete("{$this->apiBaseUrl}/{$id}");

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('admin.user.index')->with('success', $responseData['message'] ?? 'Pengguna berhasil dinonaktifkan.');
            }

            Log::error("Admin User Destroy - API request for user {$id} failed: " . $response->body());
            return back()->with('error', $responseData['message'] ?? 'Gagal menonaktifkan pengguna.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Admin User Destroy - API connection error for user {$id}: " . $e->getMessage());
            return back()->with('error', 'Tidak dapat terhubung ke layanan pengguna.');
        } catch (\Exception $e) {
            Log::error("Admin User Destroy - Generic error for user {$id}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menonaktifkan pengguna.');
        }
    }
}
