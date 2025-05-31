<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session; // Untuk flash messages

class CartController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/');
    }

    /**
     * Display the user's shopping cart.
     * Data is fetched server-side from the Node.js API.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userId = session('userId'); // Asumsi userId disimpan di session saat login
        $cartData = [];
        $error = null;
        $successMessage = Session::get('success'); // Ambil pesan sukses dari session

        if (!$userId) {
            // Jika user tidak login, bisa redirect ke halaman login atau tampilkan pesan
            // Untuk contoh ini, kita akan tetap ke view cart dengan pesan error
            $error = 'Anda harus login untuk melihat keranjang belanja.';
            return view('cart.index', compact('cartData', 'error', 'successMessage'));
        }

        try {
            // Endpoint API Node.js untuk mengambil keranjang adalah GET /api/cart/:user_id
            $response = Http::get("{$this->apiBaseUrl}/api/cart/{$userId}");

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['success']) && $responseData['success'] === true) {
                    $cartData = $responseData['data'] ?? [];
                } else {
                    Log::error('API for cart fetch was successful but returned success:false. Response: ' . $response->body());
                    $error = $responseData['message'] ?? 'Gagal memuat data keranjang dari API.';
                }
            } else {
                Log::error("Failed to fetch cart from API for user {$userId}: Status " . $response->status() . " - Body: " . $response->body());
                $error = 'Gagal memuat keranjang belanja. Status: ' . $response->status();
                if ($response->status() == 401) {
                    $error = 'Sesi Anda mungkin telah berakhir. Silakan login kembali.';
                }
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("API connection error when fetching cart for user {$userId}: " . $e->getMessage());
            $error = 'Tidak dapat terhubung ke layanan keranjang. Mohon coba lagi nanti.';
        } catch (\Exception $e) {
            Log::error("Generic error when fetching cart for user {$userId}: " . $e->getMessage());
            $error = 'Terjadi kesalahan tidak terduga saat memuat keranjang.';
        }

        return view('cart.index', compact('cartData', 'error', 'successMessage'));
    }

    /**
     * Remove an item from the user's shopping cart.
     * This action will be triggered by a form submission from the cart view.
     */
    public function removeItem(Request $request, $cartItemId)
    {
        $userId = session('userId');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengubah keranjang.');
        }

        try {
            // API Node.js untuk menghapus item adalah DELETE /api/cart/item/:cartItemId
            // dan memerlukan user_id di body untuk verifikasi kepemilikan
            $response = Http::withBody(json_encode(['user_id' => $userId]), 'application/json')
                ->delete("{$this->apiBaseUrl}/api/cart/item/{$cartItemId}");

            if ($response->successful() && ($response->json()['success'] ?? false)) {
                return redirect()->route('cart.index')->with('success', $response->json()['message'] ?? 'Item berhasil dihapus dari keranjang.');
            } else {
                Log::error("Failed to remove cart item {$cartItemId} for user {$userId}: " . $response->body());
                $errorMessage = $response->json()['message'] ?? 'Gagal menghapus item dari keranjang.';
                if ($response->status() == 403) {
                    $errorMessage = 'Anda tidak diizinkan menghapus item ini.';
                } else if ($response->status() == 404) {
                    $errorMessage = 'Item keranjang tidak ditemukan.';
                }
                return redirect()->route('cart.index')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error("Exception removing cart item {$cartItemId}: " . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat menghapus item.');
        }
    }

    /**
     * Clear all items from the user's shopping cart.
     * This action will be triggered by a form submission.
     */
    public function clearCart(Request $request)
    {
        $userId = session('userId');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengubah keranjang.');
        }

        try {
            // API Node.js untuk mengosongkan keranjang adalah DELETE /api/cart/clear/:user_id
            $response = Http::delete("{$this->apiBaseUrl}/api/cart/clear/{$userId}");

            if ($response->successful() && ($response->json()['success'] ?? false)) {
                return redirect()->route('cart.index')->with('success', $response->json()['message'] ?? 'Keranjang berhasil dikosongkan.');
            } else {
                Log::error("Failed to clear cart for user {$userId}: " . $response->body());
                return redirect()->route('cart.index')->with('error', $response->json()['message'] ?? 'Gagal mengosongkan keranjang.');
            }
        } catch (\Exception $e) {
            Log::error("Exception clearing cart for user {$userId}: " . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat mengosongkan keranjang.');
        }
    }
}
