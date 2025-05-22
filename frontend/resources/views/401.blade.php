@extends('components.layout')

@section('title')
    Tixin - Unauthorized
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="flex justify-center items-center min-h-screen bg-gray-100 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-lg bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-red-600 text-white text-center py-4">
                <h2 class="text-2xl font-semibold">401 Unauthorized</h2>
                <p class="mt-1 opacity-90 text-sm">Access Denied</p>
            </div>

            <div class="p-6 sm:p-8 text-center">
                @if (session('error'))
                    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <strong>Error</strong>
                        </div>
                        <div class="mt-1 text-sm">{{ session('error') }}</div>
                    </div>
                @else
                    <div class="mb-4 text-sm text-gray-600">
                        <p>You do not have permission to access this page.</p>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="/"
                        class="py-3 px-4 rounded-lg bg-red-600 text-white font-semibold hover:-translate-y-0.5 transition duration-200">
                        Back to Home
                    </a>
                    @if (!session('email'))
                        <a href="{{ route('login') }}"
                            class="py-3 px-4 rounded-lg border border-red-600 text-red-600 font-semibold hover:-translate-y-0.5 transition duration-200">
                            Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
