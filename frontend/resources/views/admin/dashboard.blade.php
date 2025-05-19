@extends('components.layout')

@section('title')
    Admin Dashboard
@endsection

@section('content')
    @include('components.admin.navbar')
    <div class="p-6 min-h-screen">
        <div class="mx-auto">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                    <p class="text-gray-600 mt-1">Welcome back, Administrator</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Last updated: {{ now()->format('M d, Y h:i A') }}
                    </span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Members Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6 flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Members</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['member'] ?? 0 }}</p>
                            <p class="mt-1 text-sm text-gray-500 flex items-center">
                                <span class="text-green-500 mr-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                12% from last month
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z" />
                            </svg>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                        <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all members
                            →</a>
                    </div>
                </div>

                <!-- Finance Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6 flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Revenue</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">
                                ${{ number_format($stats['finance'] ?? 0, 2) }}</p>
                            <p class="mt-1 text-sm text-gray-500 flex items-center">
                                <span class="text-green-500 mr-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                8.2% from last month
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-green-50 text-green-600">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                        <a href="#" class="text-sm font-medium text-green-600 hover:text-green-500">View financial
                            reports →</a>
                    </div>
                </div>

                <!-- Organizers Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6 flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Active Organizers</p>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats['organizer'] ?? 0 }}</p>
                            <p class="mt-1 text-sm text-gray-500 flex items-center">
                                <span class="text-green-500 mr-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                5 new this month
                            </p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
                            </svg>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">Manage organizers
                            →</a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <!-- Activity Item -->
                    <div class="px-6 py-4 flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">New member registration</p>
                                <p class="text-sm text-gray-500">2h ago</p>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">John Doe registered as a new member.</p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="px-6 py-4 flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">New event created</p>
                                <p class="text-sm text-gray-500">5h ago</p>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">"Summer Conference 2023" was created by Tech Events Inc.
                            </p>
                        </div>
                    </div>

                    <!-- Activity Item -->
                    <div class="px-6 py-4 flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">Payment received</p>
                                <p class="text-sm text-gray-500">1d ago</p>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">$1,250.00 payment from Acme Corp for event sponsorship.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 text-center">
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all activity
                        →</a>
                </div>
            </div>
        </div>
    </div>
@endsection
