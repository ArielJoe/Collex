@extends('components.layout')

@section('title')
    My Certificates
@endsection

@section('content')
    @include('components.navbar')
    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">My Certificates</h1>

            @if ($errors->has('message'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ $errors->first('message') }}</span>
                </div>
            @elseif (empty($certificates) || count($certificates) === 0)
                <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                    <p class="text-gray-600">You have no certificates yet. Attend an event to earn one!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($certificates as $certificate)
                        <div
                            class="bg-white rounded-lg shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    {{ $certificate['detail_id']['title'] ?? 'Unnamed Event' }}</h3>
                                <p class="text-gray-600 mt-2">Issued on:
                                    {{ \Carbon\Carbon::parse($certificate['uploaded_at'])->format('F d, Y') }}</p>
                                <a href="{{ $certificate['certificate_url'] }}" target="_blank"
                                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    <i class="fas fa-download mr-2"></i> Download Certificate
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('components.footer')
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush
