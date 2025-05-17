@extends('components.layout')

@section('title')
    Tixin - Login
@endsection

@section('content')
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center mb-6">Login to Tixin</h2>
            <form method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" value="{{ old('password') }}"
                        class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                        required>
                    @error('error')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    @if (true)
                        <div class="p-4 mb-4 text-green-800 bg-green-100 rounded-lg">
                            <strong>{{ session('success') }}</strong><br>
                            Email: {{ session('email') }}<br>
                            Role: {{ session('role') }}
                        </div>
                    @endif
                </div>

                <button type="submit" class="w-full bg-primary text-white p-2 rounded-md">Login</button>
            </form>
        </div>
    </div>
@endsection
