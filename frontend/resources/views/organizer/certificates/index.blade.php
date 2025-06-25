@extends('components.layout')

@section('title')
    Certificates
@endsection

@section('content')
    @include('components.navbar')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Sertifikat Peserta</h1>
                <p class="text-sm text-gray-500 mt-1">Lihat dan kelola daftar sertifikat yang telah diunggah oleh organizer.
                </p>
            </div>
            <a href="{{ route('organizer.certificates.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Sertifikat
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-md bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if (count($certificates) > 0)
            <div class="overflow-x-auto bg-white shadow-sm border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600 font-semibold">
                        <tr>
                            <th class="px-6 py-3">#</th>
                            <th class="px-6 py-3">Email Peserta</th>
                            <th class="px-6 py-3">ID Event</th>
                            <th class="px-6 py-3">Judul Sesi</th>
                            <th class="px-6 py-3">Tanggal Upload</th>
                            <th class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach ($certificates as $index => $cert)
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="px-6 py-4">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium">
                                        {{ $cert['registration_id']['user_id']['email'] ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">{{ $cert['registration_id']['event_id'] ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $cert['detail_id']['title'] ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($cert['uploaded_at'])->format('d-m-Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ $cert['certificate_url'] }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-white bg-green-600 hover:bg-green-700 rounded-md transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                            <polyline points="7 10 12 15 17 10" />
                                            <line x1="12" y1="15" x2="12" y2="3" />
                                        </svg>
                                        Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-10 flex flex-col items-center text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 text-gray-300" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4" />
                </svg>
                <p class="text-lg font-medium">Belum ada sertifikat yang diunggah.</p>
                <p class="text-sm text-gray-400">Silakan unggah sertifikat melalui tombol di atas.</p>
            </div>
        @endif
    </div>

    @include('components.footer')
@endsection
