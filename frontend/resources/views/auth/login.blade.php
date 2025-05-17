@extends('components.layout')

@section('title')
    Tixin - Login
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="flex justify-center items-center min-h-screen bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-red-600 text-white text-center py-4">
                <h2 class="text-2xl font-semibold">Welcome Back</h2>
                <p class="mt-1 opacity-90 text-sm">Login to your Tixin account</p>
            </div>

            <div class="p-6">
                @if (session('success'))
                    <div class="p-4 mb-6 rounded-lg bg-green-50 border border-green-200 text-green-800">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <strong>{{ session('success') }}</strong>
                        </div>
                        <div class="mt-2 text-sm">
                            <p>Email: {{ session('email') }}</p>
                            <p>Role: {{ session('role') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-5">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-200 focus:border-red-600 outline-none transition @error('email') @enderror"
                            placeholder="your@email.com" required autofocus>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="#" class="text-sm text-red-600 hover:underline">Forgot password?</a>
                        </div>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-200 focus:border-red-600 outline-none transition @error('password') @enderror"
                            placeholder="••••••••" required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('error')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="remember" id="remember"
                            class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-600">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 hover:-translate-y-0.5 transition-transform duration-200">
                        Login
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-gray-600">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-medium text-red-600 hover:underline">Sign up</a>
                </div>
            </div>
        </div>
    </div>
@endsection
