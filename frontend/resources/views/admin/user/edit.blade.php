@extends('components.layout')

@section('title')
    Edit {{ $user['full_name'] }}
@endsection

@section('content')
    @include('components.admin.navbar')
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8 sm:ml-64 min-h-screen">
        <div class="max-w-3xl mx-auto">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mt-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Edit User Profile</h1>
                <a href="{{ route('admin.user.index') }}"
                    class="mt-3 sm:mt-0 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Users
                </a>
            </div>

            <!-- User info card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="ml-3 text-xl font-semibold text-gray-800">{{ $user['full_name'] }}</h2>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Modify user details and permissions</p>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.user.update', $user['_id']) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <!-- Full Name -->
                            <div class="sm:col-span-3">
                                <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" id="full_name" name="full_name" value="{{ $user['full_name'] }}"
                                        required
                                        class="px-10 py-2 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 rounded-md border-gray-300">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="sm:col-span-3">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                    </div>
                                    <input type="email" id="email" name="email" value="{{ $user['email'] }}"
                                        required
                                        class="px-10 py-2 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 rounded-md border-gray-300">
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="sm:col-span-3">
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">
                                    Phone Number
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="phone_number" name="phone_number"
                                        value="{{ $user['phone_number'] }}" required pattern="[0-9]{10,12}"
                                        title="Phone number should be 10-12 digits"
                                        class="px-10 py-2 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 rounded-md border-gray-300">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Format: 10-12 digits only</p>
                            </div>

                            <!-- Role -->
                            <div class="sm:col-span-3">
                                <label for="role" class="block text-sm font-medium text-gray-700">User Role</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <select id="role" name="role" required
                                        class="px-10 py-2 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 rounded-md border-gray-300">
                                        <option value="member" {{ $user['role'] == 'member' ? 'selected' : '' }}>Member
                                        </option>
                                        <option value="finance" {{ $user['role'] == 'finance' ? 'selected' : '' }}>Finance
                                        </option>
                                        <option value="organizer" {{ $user['role'] == 'organizer' ? 'selected' : '' }}>
                                            Organizer</option>
                                    </select>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Determines user permissions in the system</p>
                            </div>

                            <!-- Password -->
                            <div class="sm:col-span-6">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Password
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="password" id="password" name="password"
                                        class="px-10 py-2 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 rounded-md border-gray-300">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end space-x-3">
                            <a href="{{ route('admin.user.index') }}"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional options card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-md font-medium text-gray-800">Additional Options</h3>
                </div>
                <div class="p-4 space-y-4">
                    {{-- <a href="{{ route('admin.user.activity', $user['_id']) }}"
                        class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        View Activity Log
                    </a> --}}
                    {{-- <form method="POST" action="{{ route('admin.user.reset-password', $user['_id']) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Send Password Reset Link
                        </button>
                    </form> --}}
                    <form method="POST" action="{{ route('admin.user.destroy', $user['_id']) }}"
                        onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                        class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="flex items-center text-red-600 hover:text-red-800 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
