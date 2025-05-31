<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/');
    }

    /**
     * Display the checkout page with cart items.
     */
    public function checkoutPage(Request $request)
    {
        $userId = session('userId');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melanjutkan ke pembayaran.');
        }

        $cartItems = [];
        $totalAmount = 0;
        $error = null;

        try {
            // Fetch cart items from Node.js API
            // Pastikan endpoint API Node.js Anda adalah /api/cart/:user_id
            $cartResponse = Http::get("{$this->apiBaseUrl}/api/cart/{$userId}");

            if ($cartResponse->successful() && isset($cartResponse->json()['success']) && $cartResponse->json()['success'] === true) {
                $fetchedItems = $cartResponse->json()['data'] ?? [];
                if (empty($fetchedItems)) {
                    return redirect()->route('cart.index')->with('info', 'Keranjang Anda kosong. Silakan tambahkan item terlebih dahulu.');
                }

                foreach ($fetchedItems as $item) {
                    $price = 0;
                    $itemPosterUrl = null; // Default jika tidak ada poster

                    // Ambil poster_url dari event_id jika ada
                    if (isset($item['event_id']) && isset($item['event_id']['poster_url']) && !empty($item['event_id']['poster_url'])) {
                        $itemPosterUrl = $item['event_id']['poster_url'];
                    }

                    if (isset($item['detail_id']) && $item['detail_id'] !== null && isset($item['detail_id']['price'])) {
                        $price = floatval($item['detail_id']['price']['$numberDecimal'] ?? ($item['detail_id']['price'] ?? 0));
                    } elseif (isset($item['package_id']) && $item['package_id'] !== null && isset($item['package_id']['price'])) {
                        $price = floatval($item['package_id']['price']['$numberDecimal'] ?? ($item['package_id']['price'] ?? 0));
                    }
                    $totalAmount += $price;

                    $cartItems[] = [
                        // Menyimpan ID asli dari item keranjang, detail, dan paket jika diperlukan nanti
                        'cart_item_id' => $item['_id'] ?? null,
                        'event_id' => $item['event_id']['_id'] ?? null,
                        'detail_id' => $item['detail_id']['_id'] ?? null,
                        'package_id' => $item['package_id']['_id'] ?? null,

                        'name' => $item['detail_id']['title'] ?? ($item['package_id']['package_name'] ?? 'Nama Item Tidak Diketahui'),
                        'price' => $price,
                        'event_name' => $item['event_id']['name'] ?? 'Event Tidak Diketahui',
                        'poster_url' => $itemPosterUrl, // Menyertakan poster_url
                    ];
                }
            } else {
                Log::error('Checkout Page - Failed to fetch cart: ' . $cartResponse->body());
                $error = $cartResponse->json()['message'] ?? 'Gagal memuat item keranjang untuk checkout.';
                return redirect()->route('cart.index')->with('error', $error);
            }
        } catch (\Exception $e) {
            Log::error('Checkout Page - Exception: ' . $e->getMessage());
            $error = 'Terjadi kesalahan saat menyiapkan halaman checkout.';
            return redirect()->route('cart.index')->with('error', $error);
        }
        return view('payment.checkout', compact('cartItems', 'totalAmount', 'error'));
    }

    /**
     * Process the cart checkout and payment proof submission.
     */
    public function processCartCheckout(Request $request)
    {
        $userId = session('userId');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $validated = $request->validate([
            'proof_url_file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:5120', // 5MB max
            'total_amount_checkout' => 'required|numeric|min:0',
            // Anda mungkin ingin mengirim array ID item keranjang yang diproses
            // 'processed_cart_item_ids' => 'nullable|array',
            // 'processed_cart_item_ids.*' => 'string' 
        ]);

        $filePath = null;
        $fullFileUrl = null;

        if ($request->hasFile('proof_url_file')) {
            $file = $request->file('proof_url_file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('payment-proofs/cart', $fileName, 'public');
            $fullFileUrl = Storage::url($filePath);
        } else {
            return back()->withInput()->with('error', 'Bukti pembayaran wajib diunggah.');
        }

        try {
            // Ambil ulang item keranjang yang akan di-checkout untuk dikirim ke API
            // Ini penting jika API memerlukan detail item yang di-checkout
            $cartResponseForCheckout = Http::get("{$this->apiBaseUrl}/api/cart/{$userId}");
            $itemsToCheckout = [];
            if ($cartResponseForCheckout->successful() && ($cartResponseForCheckout->json()['success'] ?? false)) {
                $fetchedCartItems = $cartResponseForCheckout->json()['data'] ?? [];
                foreach ($fetchedCartItems as $ci) {
                    $itemsToCheckout[] = [
                        'cart_item_id' => $ci['_id'], // ID item di koleksi Cart
                        'event_id' => $ci['event_id']['_id'],
                        'item_id'   => $ci['detail_id']['_id'] ?? ($ci['package_id']['_id'] ?? null), // ID dari detail atau package
                        'item_type' => isset($ci['detail_id']) ? 'detail' : (isset($ci['package_id']) ? 'package' : null),
                        'price'     => floatval($ci['detail_id']['price'] ?? ($ci['package_id']['price'] ?? 0)),
                    ];
                }
            } else {
                throw new \Exception('Gagal mengambil item keranjang saat proses checkout.');
            }

            if (empty($itemsToCheckout)) {
                return redirect()->route('cart.index')->with('info', 'Keranjang Anda kosong, tidak ada yang bisa di-checkout.');
            }

            $payload = [
                'user_id' => $userId,
                'total_amount' => floatval($validated['total_amount_checkout']),
                'proof_url' => $fullFileUrl,
                'cart_items' => $itemsToCheckout // Kirim detail item yang relevan
            ];

            // Ganti dengan endpoint API Node.js Anda yang sebenarnya untuk checkout keranjang
            $response = Http::post("{$this->apiBaseUrl}/api/payments/process-cart-checkout", $payload);
            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('home')->with('success', $responseData['message'] ?? 'Pembayaran Anda berhasil diproses dan sedang menunggu konfirmasi.');
            } else {
                Log::error('Process Cart Checkout - API request failed: ' . $response->body());
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                return back()->withInput()->with('error', $responseData['message'] ?? 'Gagal memproses pembayaran Anda.');
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Process Cart Checkout - API connection error: ' . $e->getMessage());
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan pembayaran.');
        } catch (\Exception $e) {
            Log::error('Process Cart Checkout - Generic error: ' . $e->getMessage());
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
