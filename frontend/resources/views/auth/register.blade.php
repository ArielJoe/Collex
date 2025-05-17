@extends('components.layout')

@section('title')
    Tixin - Register
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="flex justify-center items-center min-h-screen bg-gray-100 p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-lg bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-red-600 text-white text-center py-4">
                <h2 class="text-2xl font-semibold">Create Account</h2>
                <p class="mt-1 opacity-90 text-sm">Join Tixin today</p>
            </div>

            <div class="p-6 sm:p-8">
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <strong>Please fix these issues:</strong>
                        </div>
                        <ul class="mt-1 pl-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.submit') }}">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-200 focus:border-red-600 outline-none transition @error('full_name') @enderror"
                                placeholder="John Doe" required autofocus>
                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <div
                                class="flex rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-red-200 focus-within:border-red-600 @error('phone_number') @enderror">
                                <input type="tel" name="phone_number" id="phone_number"
                                    value="{{ old('phone_number') }}"
                                    class="flex-1 px-4 py-3 border-none rounded-r-lg outline-none"
                                    placeholder="812 3456 7890" required>
                            </div>
                            @error('phone_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-200 focus:border-red-600 outline-none transition @error('email') @enderror"
                                placeholder="your@email.com" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <input type="password" name="password" id="password"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-200 focus:border-red-600 outline-none transition @error('password') @enderror"
                                    placeholder="••••••••" required oninput="updatePasswordStrength(this.value)">
                                <div class="h-1 bg-gray-200 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full w-0 transition-all duration-200" id="password-strength-bar"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-600">8+ chars with A-Z, a-z & 0-9</p>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-1">Confirm</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-red-200 focus:border-red-600 outline-none transition"
                                    placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex items-center mt-2">
                            <input type="checkbox" name="terms" id="terms"
                                class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-600" required>
                            <label for="terms" class="ml-2 block text-sm text-gray-700">
                                I agree to the <a href="#" class="text-red-600 hover:underline">Terms</a> and
                                <a href="#" class="text-red-600 hover:underline">Privacy Policy</a>
                            </label>
                        </div>

                        <button type="submit"
                            class="sm:col-span-2 mt-4 w-full py-3 px-4 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 hover:-translate-y-0.5 transition-transform duration-200">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center text-sm text-gray-600">
                    Have an account? <a href="{{ route('login') }}" class="font-medium text-red-600 hover:underline">Sign
                        in</a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function updatePasswordStrength(password) {
                const strengthBar = document.getElementById('password-strength-bar');
                let strength = 0;

                if (password.length >= 8) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[a-z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;

                strengthBar.style.width = strength + '%';
                strengthBar.style.backgroundColor =
                    strength < 50 ? '#ef4444' :
                    strength < 75 ? '#f59e0b' : '#10b981';
            }

            document.getElementById('phone_number').addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 2 && value.length <= 5) {
                    value = value.replace(/(\d{2})(\d{0,3})/, '$1 $2');
                } else if (value.length > 5 && value.length <= 9) {
                    value = value.replace(/(\d{2})(\d{3})(\d{0,4})/, '$1 $2 $3');
                } else if (value.length > 9) {
                    value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '$1 $2 $3');
                }
                e.target.value = value;
            });
        </script>
    @endpush
@endsection
