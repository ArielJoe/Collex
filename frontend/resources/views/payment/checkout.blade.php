@extends('components.layout')

@section('title')
    Konfirmasi Pembelian & Checkout
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    @include('components.navbar')

    <div class="min-h-screen bg-gray-50 px-4 py-8 md:py-12">
        <div class="container mx-auto max-w-6xl">
            <!-- Success Message Section -->
            @if (session('success'))
                <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-green-200">
                    <div class="p-8">
                        <div class="flex flex-col items-center text-center">
                            <div class="flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                                <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h2>
                            <p class="text-gray-600 mb-6 max-w-md">
                                {{ session('success') }}
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('home') }}"
                                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition duration-200">
                                    Kembali ke Beranda
                                </a>
                                <a href="{{ route('user.transactions') }}"
                                    class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                                    Lihat Riwayat Transaksi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Header Section -->
                <div class="text-center mb-10">
                    <span class="text-xs font-semibold text-red-600 uppercase tracking-wider">KONFIRMASI PEMBELIAN
                        ANDA</span>
                    <h1 class="mt-2 text-2xl font-extrabold text-gray-900 sm:text-4xl sm:tracking-tight lg:text-4xl">
                        Selesaikan Pembayaran Anda
                    </h1>
                    <p class="max-w-xl mt-4 mx-auto text-lg text-gray-600">
                        Harap isi informasi berikut untuk melanjutkan proses checkout dan unggah bukti pembayaran Anda.
                    </p>
                </div>

                <!-- Alerts Section -->
                @if (session('error') || (isset($error) && $error))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-red-700">{{ session('error') ?? $error }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()"
                            class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-red-700 font-medium">Terdapat kesalahan dalam pengisian form:</span>
                        </div>
                        <ul class="list-disc pl-5 text-red-700">
                            @foreach ($errors->all() as $validationError)
                                <li class="text-sm">{{ $validationError }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (isset($cartItems) && count($cartItems) > 0 && !isset($error))
                    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data"
                        id="checkoutForm">
                        @csrf
                        <input type="hidden" name="total_amount_checkout" value="{{ $totalAmount }}">

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Left Column: Buyer Details and Payment Proof -->
                            <div class="lg:col-span-2 space-y-8">
                                <!-- Buyer Details Card -->
                                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-gray-200">
                                        Detail
                                        Pembeli</h2>
                                    <div class="space-y-5">
                                        <div>
                                            <label for="name" class="block mb-2 text-sm font-medium text-gray-700">Nama
                                                Lengkap:</label>
                                            <div class="relative">
                                                <input type="text" id="name" name="buyer_name"
                                                    class="w-full px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                                    value="{{ session('full_name') }}" readonly>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="email"
                                                class="block mb-2 text-sm font-medium text-gray-700">Email:</label>
                                            <div class="relative">
                                                <input type="email" id="email" name="buyer_email"
                                                    class="w-full px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                                    value="{{ session('email') }}" readonly>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="contact_no"
                                                class="block mb-2 text-sm font-medium text-gray-700">Nomor
                                                Phone Number:</label>
                                            <div class="relative">
                                                <input type="text" id="contact_no" name="buyer_contact"
                                                    class="w-full px-4 py-3 text-sm text-gray-700 bg-gray-50 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                                    value="{{ session('phone_number') }}" readonly>
                                                <div
                                                    class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Proof Card -->
                                <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-6 pb-2 border-b border-gray-200">
                                        Bukti
                                        Pembayaran</h2>
                                    <p class="text-sm text-gray-600 mb-6">Screenshot & unggah bukti pembayaran Anda di
                                        sini:
                                    </p>

                                    <!-- Payment Instructions -->
                                    <div
                                        class="bg-blue-50 border border-blue-100 text-blue-700 p-4 rounded-lg text-sm mb-6">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <div>
                                                <p class="font-medium mb-2">Silakan transfer sejumlah <span
                                                        class="text-red-600 font-bold">Rp
                                                        {{ number_format($totalAmount, 0, ',', '.') }}</span> ke rekening
                                                    berikut:</p>
                                                <ul class="list-disc pl-5 space-y-1">
                                                    <li><span class="font-medium">Bank ABC:</span> 123-456-7890 a/n Collex
                                                        Event</li>
                                                    <li><span class="font-medium">Bank XYZ:</span> 098-765-4321 a/n Collex
                                                        Event</li>
                                                </ul>
                                                <p class="mt-2">Setelah transfer, mohon unggah bukti pembayaran di bawah
                                                    ini.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- File Upload -->
                                    <div id="uploadContainer"
                                        class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center transition-colors duration-200 hover:border-red-300 bg-gray-50 hover:bg-gray-100 cursor-pointer">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="p-3 bg-red-50 rounded-full">
                                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-lg font-medium text-gray-700">Klik untuk mengunggah</p>
                                                <p class="text-sm text-gray-500">Format: PNG, JPG, GIF, PDF (Maks. 5MB)</p>
                                            </div>
                                            <input type="file" name="proof_url_file" id="proof_url_file"
                                                class="hidden" required
                                                accept="image/jpeg,image/png,image/gif,application/pdf">
                                        </div>
                                        <div id="filePreviewContainer" class="mt-4 hidden">
                                            <img id="filePreview"
                                                class="mx-auto max-h-48 rounded-lg border border-gray-200 shadow-sm"
                                                alt="File Preview">
                                            <p id="fileName" class="mt-2 text-sm font-medium text-gray-700"></p>
                                            <button type="button" id="removeFileBtn"
                                                class="mt-2 text-sm text-red-600 hover:text-red-800 font-medium">
                                                Hapus File
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Order Summary -->
                            <div class="lg:col-span-1">
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-8">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-3 border-b border-gray-200">
                                        Ringkasan Pesanan</h3>

                                    <!-- Event Poster -->
                                    @php
                                        $firstCartItem =
                                            isset($cartItems) && count($cartItems) > 0 ? $cartItems[0] : null;
                                        $eventPoster = $firstCartItem['poster_url'];
                                        $eventTitle = $firstCartItem['event_name'];
                                    @endphp
                                    <div class="mb-4 overflow-hidden rounded-lg border border-gray-200">
                                        <img src="{{ $eventPoster }}" alt="Event Poster"
                                            class="w-full h-40 object-cover hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <!-- Event Title -->
                                    <div class="mb-4">
                                        <p class="text-xs text-red-500 uppercase font-medium mb-1">Judul Event</p>
                                        <h4 class="event-title text-md font-semibold text-gray-800 leading-tight">
                                            {{ Str::limit($eventTitle, 50) }}
                                        </h4>
                                    </div>

                                    <!-- Cart Items -->
                                    <div class="mb-6">
                                        <p class="text-xs text-red-500 uppercase font-medium mb-2">Item di Keranjang</p>
                                        <div class="cart-items-container space-y-3 pr-2">
                                            @foreach ($cartItems as $item)
                                                <div class="flex items-start">
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-800">
                                                            {{ Str::limit($item['name'], 30) }}</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ Str::limit($item['event_name'], 20) }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Total Amount -->
                                    <div class="border-t border-gray-200 pt-4">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-medium text-gray-600">Subtotal</span>
                                            <span class="text-sm font-medium text-gray-800">Rp
                                                {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-medium text-gray-600">Biaya Admin</span>
                                            <span class="text-sm font-medium text-gray-800">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-4 pt-2 border-t border-gray-200">
                                            <span class="text-base font-medium text-gray-700">Total Pembayaran</span>
                                            <span class="text-xl font-bold text-red-600">Rp
                                                {{ number_format($totalAmount, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="mt-10 pt-8 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <a href="{{ route('cart.index') }}"
                                class="w-full sm:w-auto flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Kembali ke Keranjang
                            </a>
                            <button type="submit" id="submitPaymentBtn"
                                class="w-full text-center sm:w-auto flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-70">
                                <span id="buttonText">Simpan dan Lanjutkan</span>
                                <svg id="loadingSpinner" class="hidden animate-spin h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </form>
                @elseif(!isset($error))
                    <!-- Empty Cart State -->
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-red-50 mb-4">
                            <svg class="h-12 w-12 text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Keranjang Anda kosong</h3>
                        <p class="text-gray-500 mb-6">Tidak ada item untuk di-checkout. Silakan tambahkan item ke keranjang
                            terlebih dahulu.</p>
                        <a href="{{ route('cart.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Kembali ke Keranjang
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
    @include('components.footer')
@endsection

@push('script')
    {{-- <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // File Upload Handling
            const uploadContainer = document.getElementById('uploadContainer');
            const fileInput = document.getElementById('proof_url_file');
            const filePreviewContainer = document.getElementById('filePreviewContainer');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const removeFileBtn = document.getElementById('removeFileBtn');

            if (uploadContainer && fileInput) {
                uploadContainer.addEventListener('click', function() {
                    fileInput.click();
                });

                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        // Validate file size
                        if (file.size > 5 * 1024 * 1024) { // 5MB
                            alert('Ukuran file melebihi batas 5MB');
                            this.value = '';
                            return;
                        }

                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                        if (!validTypes.includes(file.type)) {
                            alert('Format file tidak didukung. Harap unggah file gambar atau PDF');
                            this.value = '';
                            return;
                        }

                        // Display file info
                        if (fileName) fileName.textContent = file.name;

                        // Show preview for images
                        if (file.type.startsWith('image/') && filePreview) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                filePreview.src = e.target.result;
                                if (filePreviewContainer) filePreviewContainer.classList.remove(
                                    'hidden');
                            }
                            reader.readAsDataURL(file);
                        } else if (filePreview && filePreviewContainer) {
                            filePreviewContainer.classList.remove('hidden');
                            filePreview.src = 'https://via.placeholder.com/200x200?text=PDF+File';
                        }

                        uploadContainer.classList.add('border-red-300', 'bg-red-50');
                    }
                });

                if (removeFileBtn) {
                    removeFileBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        fileInput.value = '';
                        if (filePreviewContainer) filePreviewContainer.classList.add('hidden');
                        uploadContainer.classList.remove('border-red-300', 'bg-red-50');
                    });
                }
            }

            // Form Submission Handling
            const checkoutForm = document.getElementById('checkoutForm');
            const submitBtn = document.getElementById('submitPaymentBtn');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            if (checkoutForm && submitBtn) {
                checkoutForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    if (buttonText) buttonText.textContent = '';
                    if (loadingSpinner) loadingSpinner.classList.remove('hidden');
                });
            }
        });
    </script>
@endpush
