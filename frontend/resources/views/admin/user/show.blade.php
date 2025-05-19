@extends('components.layout')

@section('title')
    {{ $user['full_name'] }} Details
@endsection

@section('content')
    @include('components.admin.navbar')
    <div class="max-w-3xl mx-auto p-6 sm:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-5">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">User Details</h1>
                <div class="flex space-x-3 mt-4 sm:mt-0">
                    <a href="{{ route('admin.user.edit', $user['_id']) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                        aria-label="Edit user {{ $user['full_name'] }}">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
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
            </div>

            <!-- User Details Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $user['full_name'] }}</h2>
                            <p class="text-sm text-gray-500">User ID: {{ $user['_id'] }}</p>
                        </div>
                    </div>

                    <section aria-labelledby="basic-info">
                        <h3 id="basic-info" class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user['full_name'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user['email'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user['phone_number'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Role</dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                        {{ $user['role'] }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </section>

                    {{-- <!-- Additional Options -->
                    <section aria-labelledby="additional-options" class="mt-8">
                        <h3 id="additional-options" class="text-lg font-semibold text-gray-800 mb-4">Additional Options</h3>
                        <div class="flex flex-col space-y-3">
                            <a href="{{ route('admin.user.activity', $user['_id']) }}"
                                class="flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                aria-label="View activity log for {{ $user['full_name'] }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                View Activity Log
                            </a>
                            <form method="POST" action="{{ route('admin.user.destroy', $user['_id']) }}"
                                onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center text-red-600 hover:text-red-800 transition-colors duration-200"
                                    aria-label="Delete user {{ $user['full_name'] }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete User
                                </button>
                            </form>
                        </div>
                    </section> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
