<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\File;

class PaymentController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/');

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
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
            $cartResponse = Http::get("{$this->apiBaseUrl}/api/cart/{$userId}");

            if ($cartResponse->successful() && isset($cartResponse->json()['success']) && $cartResponse->json()['success'] === true) {
                $fetchedItems = $cartResponse->json()['data'] ?? [];
                if (empty($fetchedItems)) {
                    return redirect()->route('cart.index')->with('info', 'Keranjang Anda kosong. Silakan tambahkan item terlebih dahulu.');
                }

                foreach ($fetchedItems as $item) {
                    $price = 0;
                    $itemPosterUrl = null;

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
                        'cart_item_id' => $item['_id'] ?? null,
                        'event_id' => $item['event_id']['_id'] ?? null,
                        'detail_id' => $item['detail_id']['_id'] ?? null,
                        'package_id' => $item['package_id']['_id'] ?? null,
                        'name' => $item['detail_id']['title'] ?? ($item['package_id']['package_name'] ?? 'Nama Item Tidak Diketahui'),
                        'price' => $price,
                        'event_name' => $item['event_id']['name'] ?? 'Event Tidak Diketahui',
                        'poster_url' => $itemPosterUrl,
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

        Log::debug('Full request data for checkout:', $request->all());
        Log::debug('Server upload configuration:', [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
        ]);

        if ($request->hasFile('proof_url_file')) {
            $file = $request->file('proof_url_file');
            Log::debug('File upload attempt details:', [
                'originalName' => $file->getClientOriginalName(),
                'mimeType' => $file->getClientMimeType(),
                'sizeBytes' => $file->getSize(),
                'isValidPHP' => $file->isValid(),
                'phpUploadError' => $file->getError(),
                'phpUploadErrorMessage' => $file->getErrorMessage()
            ]);

            if ($file->getError() !== UPLOAD_ERR_OK) {
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE => 'File yang diunggah melebihi batas ukuran yang diizinkan oleh server (upload_max_filesize). Periksa konfigurasi php.ini.',
                    UPLOAD_ERR_FORM_SIZE => 'File yang diunggah melebihi batas ukuran yang ditetapkan dalam form HTML.',
                    UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian.',
                    UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Direktori sementara untuk unggahan tidak ditemukan di server.',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk di server.',
                    UPLOAD_ERR_EXTENSION => 'Unggahan file dihentikan oleh ekstensi PHP.'
                ];
                $errorMsg = $uploadErrors[$file->getError()] ?? 'Terjadi error tidak dikenal saat unggah file (Kode PHP: ' . $file->getError() . ')';
                Log::error('PHP File Upload Error: ' . $errorMsg, ['php_error_code' => $file->getError()]);
                return back()->withInput()->with('error', $errorMsg);
            }
        } else {
            Log::warning('No proof_url_file found in request. Laravel validation "required" should catch this.');
        }

        $validated = $request->validate([
            'proof_url_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB, types: pdf,jpg,jpeg,png
            'total_amount_checkout' => 'required|numeric|min:0',
        ]);

        $publicPathForFile = null;
        $fullFileUrl = null;

        if ($request->hasFile('proof_url_file') && $request->file('proof_url_file')->isValid()) {
            $file = $request->file('proof_url_file');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $destinationPath = public_path('payment-proofs');

            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0775, true, true);
            }

            try {
                $file->move($destinationPath, $fileName);
                $publicPathForFile = 'payment-proofs/' . $fileName;
                $fullFileUrl = asset($publicPathForFile);
                Log::info("File uploaded to public directory: {$publicPathForFile}, URL: {$fullFileUrl}");
            } catch (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
                Log::error('Failed to move uploaded file: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Gagal menyimpan file bukti pembayaran. Mohon coba lagi.');
            }
        } else {
            $errorMessage = 'Bukti pembayaran wajib diunggah dan harus file yang valid.';
            if ($request->hasFile('proof_url_file')) {
                $errorMessage .= ' File Error Code: ' . $request->file('proof_url_file')->getError();
            }
            Log::error('Proof file is missing or invalid after Laravel validation. Error details: ' . $errorMessage);
            return back()->withInput()->with('error', $errorMessage);
        }

        try {
            $cartResponseForCheckout = Http::get("{$this->apiBaseUrl}/api/cart/{$userId}");
            $itemsToCheckout = [];
            if ($cartResponseForCheckout->successful() && ($cartResponseForCheckout->json()['success'] ?? false)) {
                $fetchedCartItems = $cartResponseForCheckout->json()['data'] ?? [];
                foreach ($fetchedCartItems as $ci) {
                    $itemsToCheckout[] = [
                        'cart_item_id' => $ci['_id'],
                        'event_id' => $ci['event_id']['_id'],
                        'item_id'   => $ci['detail_id']['_id'] ?? ($ci['package_id']['_id'] ?? null),
                        'item_type' => isset($ci['detail_id']) && $ci['detail_id'] !== null ? 'detail' : (isset($ci['package_id']) && $ci['package_id'] !== null ? 'package' : null),
                        'price'     => floatval($ci['detail_id']['price']['$numberDecimal'] ?? ($ci['detail_id']['price'] ?? ($ci['package_id']['price']['$numberDecimal'] ?? ($ci['package_id']['price'] ?? 0)))),
                    ];
                }
                $params = [
                    'transaction_details' => [
                        'order_id' => rand(),
                        'gross_amount' => floatval($validated['total_amount_checkout']),
                    ],
                    'customer_details' => [
                        'first_name' => "",
                        'email' => "",
                        'phone' => "",
                    ],
                ];

                // $snapToken = Snap::getSnapToken($params);
                // dd($snapToken);
                // return response()->json($snapToken);
            } else {
                throw new \Exception('Gagal mengambil item keranjang saat proses checkout. Respons API: ' . $cartResponseForCheckout->body());
            }

            if (empty($itemsToCheckout)) {
                if ($publicPathForFile && File::exists(public_path($publicPathForFile))) {
                    File::delete(public_path($publicPathForFile));
                    Log::info("Deleted uploaded proof file from public/{$publicPathForFile} because cart was empty for checkout.");
                }
                return redirect()->route('cart.index')->with('info', 'Keranjang Anda kosong, tidak ada yang bisa di-checkout.');
            }

            $payload = [
                'user_id' => $userId,
                'total_amount' => floatval($validated['total_amount_checkout']),
                'proof_url' => $publicPathForFile, // Store as "payment-proofs/{filename}"
                'cart_items' => $itemsToCheckout
            ];

            $response = Http::post("{$this->apiBaseUrl}/api/payment/process-cart-checkout", $payload);
            $responseData = $response->json();
            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('cart.index')->with('success', 'Pembayaran Anda berhasil diproses dan sedang menunggu konfirmasi.');
            } else {
                Log::error('Process Cart Checkout - API request failed: ' . $response->body());
                if ($publicPathForFile && File::exists(public_path($publicPathForFile))) {
                    File::delete(public_path($publicPathForFile));
                    Log::info("Deleted uploaded proof file from public/{$publicPathForFile} due to API processing failure.");
                }
                return back()->withInput()->with('error', $responseData['message'] ?? 'Gagal memproses pembayaran Anda.');
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Process Cart Checkout - API connection error: ' . $e->getMessage());
            if ($publicPathForFile && File::exists(public_path($publicPathForFile))) {
                File::delete(public_path($publicPathForFile));
            }
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan pembayaran.');
        } catch (\Exception $e) {
            Log::error('Process Cart Checkout - Generic error: ' . $e->getMessage());
            if ($publicPathForFile && File::exists(public_path($publicPathForFile))) {
                File::delete(public_path($publicPathForFile));
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
