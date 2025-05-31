@extends('components.layout')

@section('title')
    {{ $event['data']['name'] ?? 'Detail Event' }}
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #EF4444;
            /* red-500 */
            --primary-color-darker: #DC2626;
            /* red-600 */
            --primary-color-lighter: #FEE2E2;
            /* red-100 */
            --text-primary: #1F2937;
            /* gray-800 */
            --text-secondary: #4B5563;
            /* gray-600 */
            --text-muted: #6B7280;
            /* gray-500 */
            --bg-light: #F9FAFB;
            /* gray-50 */
            --border-color: #E5E7EB;
            /* gray-200 */
        }

        .event-details-card {
            transition: all 0.3s ease;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .event-image-container {
            height: 450px;
            width: 100%;
            overflow: hidden;
            position: relative;
            background-color: var(--border-color);
        }

        .event-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .event-image-container:hover .event-image {
            transform: scale(1.05);
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--primary-color);
            display: inline-block;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.25rem;
            padding: 0.75rem;
            background-color: var(--bg-light);
            border-radius: 0.5rem;
        }

        .detail-icon {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            background-color: var(--primary-color-lighter);
            border-radius: 50%;
            margin-right: 1rem;
            flex-shrink: 0;
            font-size: 1.125rem;
        }

        .detail-content h3 {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .detail-content p {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.5;
        }

        .session-card {
            background-color: #FFFFFF;
            border: 1px solid var(--border-color);
            border-left: 5px solid var(--primary-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -1px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.3s ease;
        }

        .session-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }

        .btn-add-to-cart {
            background-color: var(--primary-color);
            color: white;
            transition: background-color 0.2s ease-in-out, transform 0.1s ease, box-shadow 0.2s ease;
            font-weight: 600;
            /* semibold */
            padding: 0.6rem 1.2rem;
            /* py-2.5 px-5 */
            border-radius: 0.375rem;
            /* rounded-md */
            display: inline-flex;
            /* Untuk spinner */
            align-items: center;
            justify-content: center;
            line-height: 1.25rem;
            /* Menyamakan tinggi teks dengan spinner */
        }

        .btn-add-to-cart:hover {
            background-color: var(--primary-color-darker);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-add-to-cart:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-add-to-cart:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            background-color: #D1D5DB;
            color: #6B7280;
            box-shadow: none;
        }

        .loading-spinner-small {
            display: none;
            /* Diatur oleh JS */
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .registration-prompt {
            border-radius: 0.75rem;
            padding: 2rem;
            background-color: var(--primary-color-lighter);
            border: 1px solid var(--primary-color);
            margin-top: 2.5rem;
            text-align: center;
        }

        .registration-prompt h3 {
            color: var(--primary-color-darker);
        }

        .registration-prompt p a {
            color: var(--primary-color-darker);
            font-weight: 700;
        }

        .prose {
            color: var(--text-secondary);
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4 {
            color: var(--text-primary);
        }

        .prose strong {
            color: var(--text-primary);
        }

        .prose a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .prose a:hover {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    @include('components.navbar')

    <div class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-4 md:px-8 max-w-6xl">

            <div id="alertsContainer" class="mb-6 sticky top-20 z-50"></div>

            @if (isset($event) && isset($event['data']) && is_array($event['data']) && !empty($event['data']))
                @php
                    $eventData = $event['data'];
                @endphp
                <div class="bg-white rounded-xl shadow-xl overflow-hidden event-details-card">
                    <div class="event-image-container">
                        <img src="{{ $eventData['poster_url'] ?? 'https://picsum.photos/seed/' . ($eventData['_id'] ?? 'event') . '/1200/450' }}"
                            alt="{{ $eventData['name'] ?? 'Event Poster' }}" class="event-image">
                    </div>

                    <div class="p-6 md:p-10">
                        <div
                            class="flex flex-col lg:flex-row justify-between items-start mb-8 pb-6 border-b border-gray-200">
                            <div>
                                <h1 class="text-2xl lg:text-4xl font-bold text-gray-800 leading-tight mb-2">
                                    {{ $eventData['name'] ?? 'Nama Event Tidak Tersedia' }}
                                </h1>
                                @if (isset($eventData['faculty']) && is_array($eventData['faculty']))
                                    <p class="text-lg text-red-500 font-semibold">
                                        {{ $eventData['faculty']['name'] ?? ($eventData['faculty']['code'] ?? '') }}</p>
                                @endif
                            </div>
                            @if (isset($eventData['max_participant']) && $eventData['max_participant'] > 0)
                                <div class="mt-4 lg:mt-0 text-right">
                                    <span
                                        class="bg-blue-100 text-blue-800 text-base font-semibold px-4 py-2 rounded-lg whitespace-nowrap">
                                        Kuota:
                                        {{ $eventData['registered_participant'] ?? 0 }}/{{ $eventData['max_participant'] }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8 mb-10">
                            {{-- Detail Event Utama --}}
                            <div class="detail-item">
                                <div class="detail-icon"><i class="far fa-calendar-alt"></i></div>
                                <div class="detail-content">
                                    <h3>Tanggal & Waktu Event</h3>
                                    <p>
                                        {{ isset($eventData['start_time']) ? \Carbon\Carbon::parse($eventData['start_time'])->locale('id')->isoFormat('dddd, D MMMM YYYY') : 'N/A' }}<br>
                                        Pukul
                                        {{ isset($eventData['start_time']) ? \Carbon\Carbon::parse($eventData['start_time'])->isoFormat('HH:mm') : '' }}
                                        {{ isset($eventData['end_time']) ? ' - ' . \Carbon\Carbon::parse($eventData['end_time'])->isoFormat('HH:mm') : '' }}
                                        WIB
                                    </p>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div class="detail-content">
                                    <h3>Lokasi</h3>
                                    <p>{{ $eventData['location'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-icon"><i class="fas fa-flag-checkered"></i></div>
                                <div class="detail-content">
                                    <h3>Batas Pendaftaran</h3>
                                    <p>{{ isset($eventData['registration_deadline']) ? \Carbon\Carbon::parse($eventData['registration_deadline'])->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm [WIB]') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            @if (isset($eventData['organizer']) && is_array($eventData['organizer']))
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="fas fa-user-tie"></i></div>
                                    <div class="detail-content">
                                        <h3>Diselenggarakan Oleh</h3>
                                        <p>{{ $eventData['organizer']['full_name'] ?? ($eventData['organizer']['email'] ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if (isset($eventData['description']) && !empty($eventData['description']))
                            <div class="mb-12">
                                <h2 class="section-title">Deskripsi Event</h2>
                                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                    {!! nl2br(e($eventData['description'])) !!}
                                </div>
                            </div>
                        @endif

                        @php
                            $eventDetails = $event['data']['details'] ?? [];
                        @endphp
                        @if (is_array($eventDetails) && count($eventDetails) > 0)
                            <div class="mb-12">
                                <h2 class="section-title">Sesi / Rangkaian Acara</h2>
                                <div class="space-y-6">
                                    @foreach ($eventDetails as $index => $detail)
                                        <div class="session-card">
                                            <div class="flex flex-col sm:flex-row justify-between items-start mb-3">
                                                <h4 class="text-xl font-bold text-gray-800 mb-1 sm:mb-0">
                                                    {{ $detail['title'] ?? 'Judul Sesi' }}</h4>
                                                <p class="text-xl font-extrabold text-red-500 whitespace-nowrap">
                                                    Rp {{ number_format(floatval($detail['price'] ?? 0), 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <div class="text-gray-600 space-y-1.5 text-sm mb-4">
                                                <p><i class="far fa-calendar-alt w-5 text-center mr-2 text-gray-400"></i>
                                                    {{ isset($detail['start_time']) ? \Carbon\Carbon::parse($detail['start_time'])->isoFormat('dddd, D MMMM') : 'Tanggal tidak tersedia' }}
                                                </p>
                                                <p><i class="far fa-clock w-5 text-center mr-2 text-gray-400"></i>
                                                    {{ isset($detail['start_time']) ? \Carbon\Carbon::parse($detail['start_time'])->isoFormat('HH:mm') : '' }}
                                                    {{ isset($detail['end_time']) ? ' - ' . \Carbon\Carbon::parse($detail['end_time'])->isoFormat('HH:mm') : '' }}
                                                    WIB
                                                </p>
                                                @if (isset($detail['location']) && !empty($detail['location']))
                                                    <p><i
                                                            class="fas fa-map-marker-alt w-5 text-center mr-2 text-gray-400"></i>
                                                        {{ $detail['location'] }}</p>
                                                @endif
                                                @if (isset($detail['speaker']) && !empty($detail['speaker']))
                                                    <p><i class="fas fa-user-tie w-5 text-center mr-2 text-gray-400"></i>
                                                        Pembicara: <span
                                                            class="font-medium">{{ $detail['speaker'] }}</span></p>
                                                @endif
                                            </div>
                                            @if (isset($detail['description']) && !empty($detail['description']))
                                                <div class="mt-3 text-sm text-gray-700 prose prose-sm max-w-none">
                                                    {!! nl2br(e($detail['description'])) !!}
                                                </div>
                                            @endif

                                            {{-- Tombol Tambah ke Keranjang --}}
                                            @if (session('userId') &&
                                                    isset($eventData['registration_deadline']) &&
                                                    \Carbon\Carbon::parse($eventData['registration_deadline'])->isFuture())
                                                <div class="mt-4 text-right">
                                                    <button class="btn-add-to-cart" data-event-id="{{ $eventData['_id'] }}"
                                                        data-item-id="{{ $detail['_id'] ?? $index }}"
                                                        data-item-type="detail"
                                                        data-item-name="{{ $detail['title'] ?? 'Sesi' }}">
                                                        <span class="loading-spinner-small"></span>
                                                        <span class="button-text">Tambah ke Keranjang</span>
                                                    </button>
                                                </div>
                                            @elseif(isset($eventData['registration_deadline']) &&
                                                    !\Carbon\Carbon::parse($eventData['registration_deadline'])->isFuture())
                                                <div class="mt-4 text-right">
                                                    <span class="text-sm text-red-500 font-medium">Pendaftaran sesi ini
                                                        ditutup.</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Prompt jika tidak ada sesi dan user belum login atau pendaftaran ditutup --}}
                        @if (!(is_array($eventDetails) && count($eventDetails) > 0))
                            <div class="registration-prompt">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-ticket-alt text-4xl text-red-500 mb-4"></i>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Tertarik untuk Bergabung?</h3>
                                    @if (isset($eventData['registration_deadline']) &&
                                            \Carbon\Carbon::parse($eventData['registration_deadline'])->isFuture())
                                        @if (session('userId'))
                                            <p class="text-gray-700 mb-4 max-w-md">Event ini mungkin tidak memiliki sesi
                                                terpisah. Anda bisa melanjutkan ke proses pendaftaran umum jika tersedia
                                                atau melihat keranjang Anda.</p>
                                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                                <a href="{{ route('cart.index') }}"
                                                    class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300">
                                                    Lihat Keranjang
                                                </a>
                                                {{-- Jika ada pendaftaran umum untuk event tanpa sesi --}}
                                                {{-- <a href="#" class="inline-block bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition duration-300">
                                                Daftar Event Ini
                                            </a> --}}
                                            </div>
                                        @else
                                            <p class="text-gray-700 mb-4 max-w-md">Silakan <a href="{{ route('login') }}"
                                                    class="font-bold hover:underline text-red-500">masuk</a> atau <a
                                                    href="{{ route('register') }}"
                                                    class="font-bold hover:underline text-red-500">daftar</a> untuk
                                                melakukan registrasi.</p>
                                        @endif
                                    @else
                                        <p class="text-red-600 font-semibold text-lg">Pendaftaran untuk event ini telah
                                            ditutup.</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @else
                <div class="min-h-[60vh] flex items-center justify-center">
                    <div class="text-center max-w-md mx-auto">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-gray-400 text-2xl"></i>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Event Tidak Ditemukan</h1>
                        <p class="text-gray-600 mb-6">
                            Maaf, detail untuk event ini tidak dapat ditemukan.
                        </p>
                        <a href="{{ url('/') }}"
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                            <i class="fas fa-home"></i>
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const API_BASE_URL = @json(rtrim(env('API_BASE_URL', 'http://localhost:5000'), '/'));
            const userId = @json(session('userId'));
            const mainEventId = @json(isset($event['data']) && isset($event['data']['_id']) ? $event['data']['_id'] : null);

            function showAlert(type, message) {
                const alertId = 'alert-' + Date.now();
                const alertHtml = `
                    <div id="${alertId}" class="p-4 mb-4 rounded-md ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'} flex items-start shadow-md">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-times-circle'} mt-1 mr-3 text-xl"></i>
                        <div class="flex-1">${message}</div>
                        <button type="button" class="flex items-center justify-center -mx-1.5 -my-1.5 bg-transparent text-${type === 'success' ? 'green' : 'red'}-500 rounded-lg focus:ring-2 focus:ring-${type === 'success' ? 'green' : 'red'}-400 p-1.5 hover:bg-${type === 'success' ? 'green' : 'red'}-200 inline-flex h-8 w-8" data-dismiss-target="#${alertId}" aria-label="Close">
                           <span class="sr-only">Dismiss</span>
                           <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                           </svg>
                        </button>
                    </div>`;
                $('#alertsContainer').html(alertHtml).hide().fadeIn(300);
                setTimeout(() => {
                    $('#' + alertId).fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 7000);
            }

            $(document).on('click', '[data-dismiss-target]', function() {
                const targetSelector = $(this).data('dismiss-target');
                $(targetSelector).fadeOut(300, function() {
                    $(this).remove();
                });
            });

            $('.btn-add-to-cart').on('click', function() {
                if (!userId) {
                    showAlert('error',
                        'Anda harus login terlebih dahulu untuk menambahkan item ke keranjang.');
                    return;
                }

                const button = $(this);
                const itemId = button.data('item-id');
                const itemType = button.data('item-type');
                const itemName = button.data('item-name');

                button.prop('disabled', true);
                button.find('.button-text').hide();
                button.find('.loading-spinner-small').css('display', 'inline-block'); // Tampilkan spinner

                $.ajax({
                    url: `${API_BASE_URL}/api/cart/add`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        user_id: userId, // Kirim user_id karena middleware di Node.js dihilangkan
                        event_id: mainEventId,
                        item_id: itemId,
                        item_type: itemType
                    }),
                    success: function(response) {
                        if (response.success) {
                            showAlert('success',
                                `"${itemName}" berhasil ditambahkan ke keranjang!`);
                            // Update cart count di navbar jika ada
                            // Contoh: updateNavbarCartCount(); 
                        } else {
                            showAlert('error', response.message ||
                                'Gagal menambahkan item ke keranjang.');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Terjadi kesalahan saat menambahkan item ke keranjang.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.status === 401) {
                            errorMsg =
                                'Sesi Anda mungkin telah berakhir atau Anda tidak diizinkan. Silakan login kembali.';
                        }
                        showAlert('error', errorMsg);
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.find('.loading-spinner-small').hide();
                        button.find('.button-text').show();
                    }
                });
            });
        });
    </script>
@endpush
