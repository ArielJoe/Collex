@extends('components.layout')

@section('title')
    {{ $event['data']['name'] ?? 'Detail Event' }}
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    @include('components.navbar')

    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8 max-w-6xl">

            @if (isset($event) && isset($event['data']) && is_array($event['data']) && !empty($event['data']))
                @php
                    $eventData = $event['data'];
                @endphp

                <!-- Hero Section -->
                <div class="bg-white rounded-lg overflow-hidden shadow-sm mb-8">
                    <div class="h-64 md:h-80 overflow-hidden">
                        <img src="{{ $eventData['poster_url'] ?? 'https://via.placeholder.com/1200x400?text=Event+Poster' }}"
                            alt="{{ $eventData['name'] ?? 'Event Poster' }}" class="w-full h-full object-cover">
                    </div>

                    <div class="p-6 md:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <div class="flex-1">
                                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-4">
                                    {{ $eventData['name'] ?? 'Nama Event Tidak Tersedia' }}
                                </h1>
                                <div class="flex flex-wrap gap-4 text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-calendar-alt text-gray-400"></i>
                                        <span class="font-medium">
                                            {{ isset($eventData['start_time']) ? \Carbon\Carbon::parse($eventData['start_time'])->isoFormat('D MMMM YYYY') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        <span class="font-medium">{{ $eventData['location'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            @if (isset($eventData['max_participant']) && $eventData['max_participant'] > 0)
                                <div class="bg-gray-100 px-4 py-3 rounded-lg">
                                    <div class="text-center">
                                        <div class="text-xl font-bold text-gray-900">
                                            {{ $eventData['registered_participant'] ?? 0 }}/{{ $eventData['max_participant'] }}
                                        </div>
                                        <div class="text-sm text-gray-600">Peserta Terdaftar</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Left Column - Event Details -->
                    <div class="lg:col-span-2 space-y-8">

                        <!-- Event Information -->
                        <div class="bg-white rounded-lg p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Event</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <!-- Date & Time -->
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="far fa-calendar-alt text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Tanggal &
                                            Waktu</h3>
                                        <p class="text-gray-900 font-semibold">
                                            {{ isset($eventData['start_time']) ? \Carbon\Carbon::parse($eventData['start_time'])->isoFormat('dddd, D MMMM YYYY') : 'N/A' }}
                                        </p>
                                        <p class="text-gray-600">
                                            {{ isset($eventData['start_time']) ? \Carbon\Carbon::parse($eventData['start_time'])->isoFormat('HH:mm') : '' }}
                                            {{ isset($eventData['end_time']) ? ' - ' . \Carbon\Carbon::parse($eventData['end_time'])->isoFormat('HH:mm') : '' }}
                                            WIB
                                        </p>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Lokasi
                                        </h3>
                                        <p class="text-gray-900 font-semibold">{{ $eventData['location'] ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <!-- Registration Deadline -->
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-clock text-gray-600"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">Batas
                                            Pendaftaran</h3>
                                        <p class="text-gray-900 font-semibold">
                                            {{ isset($eventData['registration_deadline']) ? \Carbon\Carbon::parse($eventData['registration_deadline'])->isoFormat('D MMMM YYYY, HH:mm [WIB]') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Faculty -->
                                @if (isset($eventData['faculty']) && is_array($eventData['faculty']))
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-university text-gray-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">
                                                Fakultas</h3>
                                            <p class="text-gray-900 font-semibold">
                                                {{ $eventData['faculty']['name'] ?? ($eventData['faculty']['code'] ?? 'N/A') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Description Section -->
                        @if (isset($eventData['description']) && !empty($eventData['description']))
                            <div class="bg-white rounded-lg p-6 shadow-sm">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi Event</h2>
                                <div class="prose max-w-none text-gray-700 leading-relaxed">
                                    {!! nl2br(e($eventData['description'])) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Event Sessions -->
                        @php
                            $eventDetails = $event['data']['details'] ?? [];
                        @endphp
                        @if (is_array($eventDetails) && count($eventDetails) > 0)
                            <div class="bg-white rounded-lg p-6 shadow-sm">
                                <h2 class="text-xl font-semibold text-gray-900 mb-6">Sesi / Rangkaian Acara</h2>
                                <div class="space-y-4">
                                    @foreach ($eventDetails as $index => $detail)
                                        <div class="border border-gray-200 rounded-lg p-5">
                                            <div class="flex flex-col sm:flex-row justify-between items-start mb-3">
                                                <h4 class="text-lg font-semibold text-gray-900 mb-2 sm:mb-0">
                                                    {{ $detail['title'] ?? 'Judul Sesi' }}
                                                </h4>
                                                <div class="text-lg font-bold text-gray-900">
                                                    Rp {{ number_format(floatval($detail['price'] ?? 0), 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <i class="far fa-calendar-alt w-4"></i>
                                                    <span>
                                                        {{ isset($detail['start_time']) ? \Carbon\Carbon::parse($detail['start_time'])->isoFormat('D MMMM YYYY') : 'Tanggal tidak tersedia' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <i class="far fa-clock w-4"></i>
                                                    <span>
                                                        {{ isset($detail['start_time']) ? \Carbon\Carbon::parse($detail['start_time'])->isoFormat('HH:mm') : '' }}
                                                        {{ isset($detail['end_time']) ? ' - ' . \Carbon\Carbon::parse($detail['end_time'])->isoFormat('HH:mm') : '' }}
                                                        WIB
                                                    </span>
                                                </div>
                                                @if (isset($detail['location']) && !empty($detail['location']))
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-map-marker-alt w-4"></i>
                                                        <span>{{ $detail['location'] }}</span>
                                                    </div>
                                                @endif
                                                @if (isset($detail['speaker']) && !empty($detail['speaker']))
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-user-tie w-4"></i>
                                                        <span>{{ $detail['speaker'] }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if (isset($detail['description']) && !empty($detail['description']))
                                                <div class="mt-3 pt-3 border-t border-gray-100 text-sm text-gray-700">
                                                    {!! nl2br(e($detail['description'])) !!}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column - Registration Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8 space-y-6">

                            <!-- Registration Card -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                                <div class="p-6 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">Pendaftaran</h3>
                                </div>

                                <div class="p-6">
                                    @if (isset($eventData['registration_deadline']) &&
                                            \Carbon\Carbon::parse($eventData['registration_deadline'])->isFuture())
                                        @if (session('userId'))
                                            <div class="space-y-4">
                                                <div class="text-center pb-4 border-b border-gray-100">
                                                    <div class="text-2xl font-bold text-gray-900 mb-1">
                                                        @if (isset($eventDetails) && count($eventDetails) > 0)
                                                            Rp
                                                            {{ number_format(array_sum(array_column($eventDetails, 'price')), 0, ',', '.') }}
                                                        @else
                                                            Gratis
                                                        @endif
                                                    </div>
                                                    <div class="text-gray-500 text-sm">Total Biaya</div>
                                                </div>

                                                <a href="#"
                                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                                    Daftar Sekarang
                                                </a>

                                                <div class="text-center text-xs text-gray-500">
                                                    <i class="fas fa-shield-alt mr-1"></i>
                                                    Pembayaran aman dan terpercaya
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center space-y-4">
                                                <p class="text-gray-700 mb-4">
                                                    Silakan masuk atau daftar untuk melakukan registrasi
                                                </p>
                                                <div class="space-y-3">
                                                    <a href="{{ route('login') }}"
                                                        class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                                        Masuk
                                                    </a>
                                                    <a href="{{ route('register') }}"
                                                        class="w-full border border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 font-semibold py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                                        Daftar Akun
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                            <i class="fas fa-exclamation-triangle text-red-500 mb-2"></i>
                                            <p class="text-red-700 font-semibold">Pendaftaran Ditutup</p>
                                            <p class="text-red-600 text-sm mt-1">Batas waktu pendaftaran telah berakhir</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Event Stats -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Status</span>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium">
                                            Tersedia
                                        </span>
                                    </div>
                                    @if (isset($eventData['max_participant']) && $eventData['max_participant'] > 0)
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Sisa Kuota</span>
                                            <span class="font-semibold text-gray-900">
                                                {{ $eventData['max_participant'] - ($eventData['registered_participant'] ?? 0) }}
                                                orang
                                            </span>
                                        </div>
                                    @endif
                                    @if (isset($eventData['organizer']) && is_array($eventData['organizer']))
                                        <div class="pt-3 border-t border-gray-100">
                                            <span class="text-gray-600 text-sm">Penyelenggara</span>
                                            <p class="font-semibold text-gray-900">
                                                {{ $eventData['organizer']['full_name'] ?? ($eventData['organizer']['email'] ?? 'N/A') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Error State -->
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
