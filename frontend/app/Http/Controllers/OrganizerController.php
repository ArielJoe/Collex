<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Untuk mendapatkan URL file jika disimpan secara lokal
use Illuminate\Validation\Rule;

class OrganizerController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        // Menggunakan API_BASE_URL dari .env dan menambahkan /api/events
        $this->apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/event';
    }

    public function index()
    {
        // Halaman dashboard organizer, bisa menampilkan ringkasan atau statistik event organizer
        // Untuk saat ini, kita bisa arahkan ke daftar event atau tampilkan view dashboard sederhana.
        // Jika ingin menampilkan event di sini juga, Anda bisa mereplikasi logika dari method events().
        return view('organizer.index');
    }

    public function events(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10); // Jumlah item per halaman
        $search = $request->query('search');

        try {
            $queryParams = [
                'page' => $page,
                'limit' => $limit,
                // Menggunakan 'organizer' sebagai key, sesuai dengan field di skema Event Node.js
                // dan API endpoint GET /api/events yang seharusnya mendukung filter ini.
                'organizer' => session()->get('userId')
            ];

            if ($search) {
                $queryParams['search'] = $search;
            }

            // Pemanggilan API ke endpoint yang benar (this->apiBaseUrl sudah /api/events)
            $response = Http::get($this->apiBaseUrl, $queryParams);

            if ($response->failed()) {
                Log::error('Organizer Events - API request failed: Status ' . $response->status() . ' - Body: ' . $response->body());
                return back()->with('error', 'Gagal mengambil daftar event Anda. Kode Status: ' . $response->status());
            }

            $responseData = $response->json();

            // Memastikan respons API memiliki struktur yang diharapkan
            if (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data'])) {
                return view('organizer.events.index', [
                    'events' => $responseData['data'], // Data event ada di dalam 'data'
                    'totalPages' => $responseData['totalPages'] ?? 1,
                    'currentPage' => $responseData['currentPage'] ?? 1,
                    'totalEvents' => $responseData['totalEvents'] ?? 0,
                    'searchFilter' => $search
                ]);
            }

            // Jika success false atau struktur data tidak sesuai
            Log::error('Organizer Events - API returned success:false or unexpected structure: ' . $response->body());
            return back()->with('error', $responseData['message'] ?? 'Gagal mengambil data event dari API.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Organizer Events - API connection error: ' . $e->getMessage());
            return back()->with('error', 'Tidak dapat terhubung ke layanan event. Mohon coba lagi nanti.');
        } catch (\Exception $e) {
            Log::error('Organizer Events - Generic error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan tidak terduga saat mengambil daftar event.');
        }
    }

    public function createEvent()
    {
        // Anda mungkin perlu mengambil daftar fakultas dari API untuk ditampilkan di form
        $faculties = [];
        try {
            $facultyResponse = Http::get(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/faculties'); // Asumsi endpoint fakultas
            if ($facultyResponse->successful() && isset($facultyResponse->json()['data'])) {
                $faculties = $facultyResponse->json()['data'];
            } else {
                Log::warning('Organizer Create Event - Failed to fetch faculties: ' . $facultyResponse->body());
            }
        } catch (\Exception $e) {
            Log::error('Organizer Create Event - Exception fetching faculties: ' . $e->getMessage());
        }
        return view('organizer.events.create', compact('faculties'));
    }

    public function storeEvent(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'max_participant' => 'required|integer|min:1',
            'faculty' => 'required|string|in:FK,FKG,FP,FTRC,FHIK,FHBD',
            'registration_deadline' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
            'start_time' => 'required|date_format:Y-m-d\TH:i|after:registration_deadline', // Ensure required
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',             // Ensure required
            'description' => 'nullable|string',
            'details.*.title' => 'required|string|max:255',
            'details.*.start_time' => 'required|date_format:Y-m-d\TH:i',
            'details.*.end_time' => 'required|date_format:Y-m-d\TH:i|after:details.*.start_time',
            'details.*.location' => 'required|string|max:255',
            'details.*.speaker' => 'required|string|max:255',
            'details.*.description' => 'required|string',
            'details.*.price' => 'required|numeric',
        ]);

        // Fetch faculty ObjectId based on the code
        $facultyResponse = Http::get(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/faculty/code/' . $validatedData['faculty']);
        $facultyData = $facultyResponse->json();

        Log::info('Faculty lookup response: ', ['url' => $facultyResponse->effectiveUri(), 'status' => $facultyResponse->status(), 'body' => $facultyData]);

        if ($facultyResponse->failed() || !isset($facultyData['success']) || !$facultyData['success']) {
            return back()->withInput()->with('error', 'Invalid faculty code or server error. Details: ' . ($facultyData['message'] ?? 'No message'));
        }

        $facultyId = $facultyData['data']['_id'];

        $payload = array_merge($validatedData, [
            'organizer' => session()->get('userId'),
            'faculty' => $facultyId,
        ]);

        // Handle file upload
        if ($request->hasFile('poster')) {
            // Define the destination path in the public directory
            $destinationPath = public_path('event_posters');
            $fileName = time() . '_' . $request->file('poster')->getClientOriginalName(); // Unique filename
            $request->file('poster')->move($destinationPath, $fileName);

            // Generate the public URL
            $payload['poster_url'] = asset('event_posters/' . $fileName);
        } else {
            // Default image if none provided
            $payload['poster_url'] = 'https://assets.grok.com/users/678dc0c1-1c78-46f4-9fee-e3b4f5e52f0b/5O2ZUDnnMKbqF0wD-profile-picture.webp';
        }

        // Remove the poster field from payload as we don't store it in DB
        unset($payload['poster']);
        unset($payload['details']);

        Log::info('Payload sent to API: ', $payload); // Debug payload

        try {
            $eventResponse = Http::post($this->apiBaseUrl, $payload);
            $eventResponseData = $eventResponse->json();

            if ($eventResponse->failed()) {
                Log::error('Event creation failed: ' . $eventResponse->body());
                return back()->withInput()->with('error', $eventResponseData['message'] ?? 'Gagal membuat event utama. Status: ' . $eventResponse->status());
            }

            if (!isset($eventResponseData['success']) || !$eventResponseData['success']) {
                Log::error('Event creation success false: ' . $eventResponse->body());
                return back()->withInput()->with('error', $eventResponseData['message'] ?? 'Gagal membuat event utama.');
            }

            $eventId = $eventResponseData['data']['_id'];

            $detailsPayload = $request->input('details', []);
            foreach ($detailsPayload as $detail) {
                $detailResponse = Http::post(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/event/details', [
                    'event_id' => $eventId,
                    'title' => $detail['title'],
                    'start_time' => $detail['start_time'],
                    'end_time' => $detail['end_time'],
                    'location' => $detail['location'],
                    'speaker' => $detail['speaker'],
                    'description' => $detail['description'],
                    'price' => $detail['price'],
                ]);

                $detailResponseData = $detailResponse->json();
                if ($detailResponse->failed() || !isset($detailResponseData['success']) || !$detailResponseData['success']) {
                    Log::error('Detail creation failed: ' . $detailResponse->body());
                    return back()->withInput()->with('error', $detailResponseData['message'] ?? 'Gagal membuat detail event.');
                }
            }

            return redirect()->route('organizer.events.index')->with('success', $eventResponseData['message'] ?? 'Event dan detail berhasil dibuat.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('API connection error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan event.');
        } catch (\Exception $e) {
            Log::error('Generic error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat membuat event.');
        }
    }

    public function showEvent($id)
    {
        try {
            $response = Http::get("{$this->apiBaseUrl}/{$id}"); // GET ke /api/events/{id}

            if ($response->failed()) {
                Log::error("Organizer Show Event - API request for event {$id} failed: " . $response->body());
                if ($response->status() == 404) {
                    return redirect()->route('organizer.events.index')->with('error', 'Event tidak ditemukan.');
                }
                return back()->with('error', 'Gagal mengambil detail event. Status: ' . $response->status());
            }

            $responseData = $response->json();
            if (isset($responseData['success']) && $responseData['success'] === true && isset($responseData['data'])) {
                $event = ['data' => $responseData['data']]; // Bungkus dalam 'data' agar konsisten dengan view
                // Jika API sudah mengembalikan event_details ter-nesting di $responseData['data']['details']
                // maka tidak perlu panggilan API tambahan di sini.
                // Jika tidak, Anda perlu mengambilnya terpisah:
                // $detailsResponse = Http::get(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . "/api/event-details/by-event/{$id}");
                // if ($detailsResponse->successful() && isset($detailsResponse->json()['data'])) {
                //    $event['data']['details'] = $detailsResponse->json()['data'];
                // } else {
                //    $event['data']['details'] = [];
                // }
                return view('organizer.events.show', compact('event'));
            }

            return redirect()->route('organizer.events.index')->with('error', $responseData['message'] ?? 'Gagal mengambil detail event.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Organizer Show Event - API connection error for event {$id}: " . $e->getMessage());
            return redirect()->route('organizer.events.index')->with('error', 'Tidak dapat terhubung ke layanan event.');
        } catch (\Exception $e) {
            Log::error("Organizer Show Event - Generic error for event {$id}: " . $e->getMessage());
            return redirect()->route('organizer.events.index')->with('error', 'Terjadi kesalahan.');
        }
    }

    public function editEvent($id)
    {
        try {
            $eventResponse = Http::get("{$this->apiBaseUrl}/{$id}"); // GET ke /api/events/{id}
            if ($eventResponse->failed()) {
                Log::error("Organizer Edit Event - API request for event {$id} failed: " . $eventResponse->body());
                if ($eventResponse->status() == 404) {
                    return redirect()->route('organizer.events.index')->with('error', 'Event tidak ditemukan.');
                }
                return redirect()->route('organizer.events.index')->with('error', 'Gagal mengambil data event untuk diedit.');
            }

            $eventResponseData = $eventResponse->json();
            if (isset($eventResponseData['success']) && $eventResponseData['success'] === true && isset($eventResponseData['data'])) {
                $event = ['data' => $eventResponseData['data']]; // Bungkus dalam 'data'

                // Ambil juga daftar fakultas untuk dropdown di form edit
                $faculties = [];
                $facultyResponse = Http::get(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/faculties');
                if ($facultyResponse->successful() && isset($facultyResponse->json()['data'])) {
                    $faculties = $facultyResponse->json()['data'];
                } else {
                    Log::warning('Organizer Edit Event - Failed to fetch faculties: ' . $facultyResponse->body());
                }
                return view('organizer.events.edit', compact('event', 'faculties'));
            }

            return redirect()->route('organizer.events.index')->with('error', $eventResponseData['message'] ?? 'Gagal mengambil data event.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Organizer Edit Event - API connection error for event {$id}: " . $e->getMessage());
            return redirect()->route('organizer.events.index')->with('error', 'Tidak dapat terhubung ke layanan event.');
        } catch (\Exception $e) {
            Log::error("Organizer Edit Event - Generic error for event {$id}: " . $e->getMessage());
            return redirect()->route('organizer.events.index')->with('error', 'Terjadi kesalahan.');
        }
    }

    public function updateEvent(Request $request, $id)
    {
        // Validasi data event utama
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'max_participants' => 'sometimes|required|integer|min:1',
            'faculty' => 'sometimes|required|string',
            'registration_deadline' => 'sometimes|required|date_format:Y-m-d\TH:i',
            'start_time' => 'sometimes|required|date_format:Y-m-d\TH:i',
            'end_time' => 'sometimes|required|date_format:Y-m-d\TH:i|after:start_time',
            'description' => 'nullable|string',
            // Validasi untuk sessions
            'sessions.*.id' => 'required|string', // id bisa "new" atau ObjectId
            'sessions.*.title' => 'required|string|max:255',
            'sessions.*.start_time' => 'required|date_format:Y-m-d\TH:i',
            'sessions.*.end_time' => 'required|date_format:Y-m-d\TH:i|after:sessions.*.start_time',
            'sessions.*.location' => 'required|string|max:255',
            'sessions.*.speaker' => 'required|string|max:255',
            'sessions.*.description' => 'required|string',
            'sessions.*.price' => 'required|numeric',
        ]);

        try {
            // Proses data event utama
            $payload = $request->only(array_keys($validatedData));
            unset($payload['sessions']); // Hapus sessions dari payload event utama

            if ($request->hasFile('poster')) {
                $path = $request->file('poster')->store('public/event_posters');
                $payload['poster_url'] = Storage::url($path);
            }
            unset($payload['poster']);

            // Validasi tanggal kondisional
            if (isset($payload['start_time']) && isset($payload['registration_deadline'])) {
                if (strtotime($payload['start_time']) <= strtotime($payload['registration_deadline'])) {
                    return back()->withInput()->withErrors(['start_time' => 'Waktu mulai event harus setelah batas waktu pendaftaran.']);
                }
            }
            if (isset($payload['end_time']) && isset($payload['start_time'])) {
                if (strtotime($payload['end_time']) <= strtotime($payload['start_time'])) {
                    return back()->withInput()->withErrors(['end_time' => 'Waktu selesai event harus setelah waktu mulai.']);
                }
            }

            // Update event utama
            $response = Http::put("{$this->apiBaseUrl}/{$id}", $payload);
            $responseData = $response->json();

            if ($response->failed() || !isset($responseData['success']) || !$responseData['success']) {
                Log::error("Organizer Update Event - API request for event {$id} failed: " . $response->body());
                return back()->withInput()->with('error', $responseData['message'] ?? 'Gagal memperbarui event.');
            }

            // Ambil daftar sesi yang sudah ada dari API
            $existingDetailsResponse = Http::get(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . "/api/event-details/by-event/{$id}");
            $existingDetails = [];
            if ($existingDetailsResponse->successful() && isset($existingDetailsResponse->json()['data'])) {
                $existingDetails = $existingDetailsResponse->json()['data'];
            } else {
                Log::warning("Failed to fetch existing event details for event {$id}: " . $existingDetailsResponse->body());
            }

            // Proses sesi
            $submittedSessionIds = collect($request->input('sessions', []))->pluck('id')->toArray();
            $existingSessionIds = collect($existingDetails)->pluck('_id')->toArray();

            // Sesi yang dihapus: ada di database tapi tidak ada di form
            $sessionsToDelete = array_diff($existingSessionIds, $submittedSessionIds);

            // Hapus sesi yang tidak ada di form
            foreach ($sessionsToDelete as $detailId) {
                $deleteResponse = Http::delete(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . "/api/event/details/{$detailId}");
                if ($deleteResponse->failed()) {
                    Log::error("Failed to delete session {$detailId}: " . $deleteResponse->body());
                    return back()->withInput()->with('error', 'Gagal menghapus sesi.');
                }
            }

            // Proses sesi yang dikirim
            foreach ($request->input('sessions', []) as $session) {
                $sessionPayload = [
                    'event_id' => $id,
                    'title' => $session['title'],
                    'start_time' => $session['start_time'],
                    'end_time' => $session['end_time'],
                    'location' => $session['location'],
                    'speaker' => $session['speaker'],
                    'description' => $session['description'],
                    'price' => $session['price'],
                ];

                if ($session['id'] === 'new') {
                    // Tambah sesi baru
                    $detailResponse = Http::post(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . '/api/event/details', $sessionPayload);
                    $detailResponseData = $detailResponse->json();
                    if ($detailResponse->failed() || !isset($detailResponseData['success']) || !$detailResponseData['success']) {
                        Log::error('Detail creation failed: ' . $detailResponse->body());
                        return back()->withInput()->with('error', $detailResponseData['message'] ?? 'Gagal menambahkan sesi baru.');
                    }
                } else {
                    // Update sesi yang sudah ada
                    $detailResponse = Http::put(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/') . "/api/event/details/{$session['id']}", $sessionPayload);
                    $detailResponseData = $detailResponse->json();
                    if ($detailResponse->failed() || !isset($detailResponseData['success']) || !$detailResponseData['success']) {
                        Log::error("Detail update failed for session {$session['id']}: " . $detailResponse->body());
                        return back()->withInput()->with('error', $detailResponseData['message'] ?? 'Gagal memperbarui sesi.');
                    }
                }
            }

            return redirect()->route('organizer.events.index')->with('success', 'Event dan sesi berhasil diperbarui.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Organizer Update Event - API connection error for event {$id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan event.');
        } catch (\Exception $e) {
            Log::error("Organizer Update Event - Generic error for event {$id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui event.');
        }
    }

    public function destroyEvent($id)
    {
        try {
            $response = Http::delete("{$this->apiBaseUrl}/{$id}"); // DELETE ke /api/events/{id}
            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('organizer.events.index')->with('success', $responseData['message'] ?? 'Event berhasil dihapus.');
            }

            Log::error("Organizer Destroy Event - API request for event {$id} failed: " . $response->body());
            return back()->with('error', $responseData['message'] ?? 'Gagal menghapus event.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Organizer Destroy Event - API connection error for event {$id}: " . $e->getMessage());
            return back()->with('error', 'Tidak dapat terhubung ke layanan event.');
        } catch (\Exception $e) {
            Log::error("Organizer Destroy Event - Generic error for event {$id}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus event.');
        }
    }

    public function createCertificate()
    {
        try {
            $response = Http::get('http://localhost:5000/api/certificates/eligible/' . session()->get('userId'));
            $data = $response->json();

            if (!$response->successful() || !isset($data['success']) || !$data['success']) {
                return back()->with('error', 'Gagal mengambil data registrasi peserta yang telah hadir.');
            }

            $registrations = $data['data'] ?? [];

            if (empty($registrations)) {
                return view('organizer.certificates.create', [
                    'registrations' => [],
                    'info' => 'Tidak ada peserta yang telah hadir untuk acara yang telah selesai.'
                ]);
            }

            return view('organizer.certificates.create', [
                'registrations' => $registrations
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil registrasi: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil data registrasi.');
        }
    }

    public function storeCertificate(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|string',
            'certificate' => 'required|file|mimes:pdf|max:2048',
        ]);

        try {
            // Upload PDF ke public/certificates
            $file = $request->file('certificate');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Pastikan folder public/certificates ada
            $file->move(public_path('certificates'), $fileName);
            $fileUrl = asset('certificates/' . $fileName); // URL untuk dilihat user

            // Ambil detail_id dari registration lewat API
            $registrationApiUrl = 'http://localhost:5000' . '/api/registration/' . $request->registration_id . '?user_id=' . session()->get('userId');
            $regResponse = Http::get($registrationApiUrl);

            // dd($regResponse->json());

            if (!$regResponse->successful()) {
                return back()->with('error', 'Gagal mengambil data registrasi.');
            }

            $regData = $regResponse->json();

            // Validasi struktur data
            if (!isset($regData['data']['item']['_id'])) {
                return back()->with('error', 'Detail acara tidak ditemukan dalam data registrasi.');
            }

            $detailId = $regData['data']['item']['_id'];

            // Siapkan payload ke API Node.js
            $payload = [
                'registration_id' => $request->registration_id,
                'detail_id'       => $detailId,
                'certificate_url' => $fileUrl,
                'uploaded_by'     => session()->get('userId'),
            ];

            // Kirim ke API /api/certificates
            $apiResponse = Http::post('http://localhost:5000' . '/api/certificates', $payload);
            $apiData = $apiResponse->json();

            if (!$apiResponse->successful() || !$apiData['success']) {
                return back()->with('error', $apiData['message'] ?? 'Gagal mengunggah sertifikat.');
            }

            return redirect()->route('organizer.events.index')
                ->with('success', 'Sertifikat berhasil diunggah.');
        } catch (\Exception $e) {
            dd($e);
            Log::error('Upload sertifikat gagal: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunggah sertifikat.');
        }
    }

    public function certificatesIndex()
    {
        try {
            $response = Http::get('http://localhost:5000' . '/api/certificates/organizer/' . session()->get('userId'));
            $data = $response->json();

            if (!$response->successful() || !$data['success']) {
                return back()->with('error', 'Gagal mengambil daftar sertifikat.');
            }

            return view('organizer.certificates.index', [
                'certificates' => $data['data']
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data sertifikat: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengambil sertifikat.');
        }
    }
}
