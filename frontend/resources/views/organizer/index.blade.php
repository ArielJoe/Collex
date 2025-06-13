@extends('components.layout')

@section('title')
    Event Organizer Dashboard
@endsection

@section('content')
    @include('components.navbar')
    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Event Organizer Dashboard</h1>
                    <p class="text-lg text-gray-600">Manage your events and track performance</p>
                </div>
                <div class="relative">
                    <button
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span>Quick Actions</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-56 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10 hidden">
                        <div class="py-1">
                            <a href="{{ route('organizer.events.create') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Create Event</a>
                            <a href="{{ route('organizer.events.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View All Events</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View
                                Reports</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Events -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Events</p>
                                <h3 class="mt-2 text-3xl font-semibold text-gray-900">24</h3>
                            </div>
                            <div class="p-3 rounded-lg bg-blue-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <a href="{{ route('organizer.events.index') }}"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            View all events
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Upcoming</p>
                                <h3 class="mt-2 text-3xl font-semibold text-gray-900">5</h3>
                            </div>
                            <div class="p-3 rounded-lg bg-yellow-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <a href="#"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            View upcoming
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Attendees -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Attendees</p>
                                <h3 class="mt-2 text-3xl font-semibold text-gray-900">1,245</h3>
                            </div>
                            <div class="p-3 rounded-lg bg-green-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <a href="#"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            View analytics
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Revenue</p>
                                <h3 class="mt-2 text-3xl font-semibold text-gray-900">$12,450</h3>
                            </div>
                            <div class="p-3 rounded-lg bg-cyan-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-cyan-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <a href="#"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                            View reports
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Action Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Create Event -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 rounded-lg bg-blue-50 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Create New Event</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Set up a new event, configure tickets, and start promoting to your
                            audience.</p>
                        <a href="{{ route('organizer.events.create') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create Event
                        </a>
                    </div>
                </div>

                <!-- Manage Events -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 rounded-lg bg-green-50 mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Manage Events</h3>
                        </div>
                        <p class="text-gray-600 mb-6">View, edit, and manage all your current and past events in one place.
                        </p>
                        <a href="{{ route('organizer.events.index') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            View All Events
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Events Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Events</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Event Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Attendees</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">Tech Conference 2023</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">Nov 15, 2023</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">San Francisco, CA</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">450</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Manage</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">Music Festival</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">Dec 5, 2023</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">New York, NY</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">1,200</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Manage</button>
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">Business Workshop</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">Oct 20, 2023</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">Chicago, IL</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">85</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Completed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 text-center">
                    <a href="{{ route('organizer.events.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        View All Events
                    </a>
                </div>
            </div>

            <!-- Scan QR Section -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Scan QR for Attendance</h3>
                </div>
                <div class="p-6">
                    <button id="toggleQrScanner"
                        class="mb-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L19.5 13" />
                        </svg>
                        Start QR Scanner
                    </button>

                    <div id="qrScannerContainer" class="hidden mt-4">
                        <div class="relative">
                            <video id="qrVideo"
                                class="w-full h-64 rounded-lg border border-gray-200 bg-gray-100"></video>
                            <div id="qrResult" class="mt-4 p-4 bg-gray-50 rounded-lg text-center text-gray-700">
                                Scan a QR code to check attendance.
                            </div>
                            <div class="mt-4 flex justify-center gap-4">
                                <button id="confirmAttendance"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                                    disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Confirm Attendance
                                </button>
                                <button id="cancelScan"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleQrScanner = document.getElementById('toggleQrScanner');
                const qrScannerContainer = document.getElementById('qrScannerContainer');
                const qrVideo = document.getElementById('qrVideo');
                const qrResult = document.getElementById('qrResult');
                const confirmAttendance = document.getElementById('confirmAttendance');
                const cancelScan = document.getElementById('cancelScan');

                let html5QrcodeScanner = null;

                toggleQrScanner.addEventListener('click', function() {
                    if (qrScannerContainer.classList.contains('hidden')) {
                        qrScannerContainer.classList.remove('hidden');
                        startQrScanner();
                    } else {
                        stopQrScanner();
                        qrScannerContainer.classList.add('hidden');
                        qrResult.textContent = 'Scan a QR code to check attendance.';
                        confirmAttendance.disabled = true;
                    }
                });

                cancelScan.addEventListener('click', function() {
                    stopQrScanner();
                    qrScannerContainer.classList.add('hidden');
                    qrResult.textContent = 'Scan a QR code to check attendance.';
                    confirmAttendance.disabled = true;
                });

                function startQrScanner() {
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear();
                    }
                    html5QrcodeScanner = new Html5Qrcode("qrVideo");
                    html5QrcodeScanner.start({
                            facingMode: "environment"
                        }, // Use rear camera
                        {
                            fps: 10,
                            qrbox: 250
                        },
                        (decodedText, decodedResult) => {
                            qrResult.textContent = `Scanned: ${decodedText}`;
                            confirmAttendance.disabled = false;
                        },
                        (error) => {
                            console.warn(error);
                        }
                    ).catch(err => {
                        console.error('Failed to start QR scanner:', err);
                        qrResult.textContent =
                            'Error starting scanner. Please ensure camera access is granted.';
                    });
                }

                function stopQrScanner() {
                    if (html5QrcodeScanner) {
                        html5QrcodeScanner.clear();
                        html5QrcodeScanner = null;
                    }
                }

                confirmAttendance.addEventListener('click', function() {
                    const scannedData = qrResult.textContent.replace('Scanned: ', '');
                    // TODO: Implement API call to confirm attendance (e.g., PATCH /api/attendance/:id/scan)
                    console.log('Confirming attendance for:', scannedData);
                    qrResult.textContent = `Attendance confirmed for ${scannedData}`;
                    confirmAttendance.disabled = true;
                    // Optionally, stop the scanner or refresh the page
                    // stopQrScanner();
                });
            });
        </script>
    @endpush

    @include('components.footer')
@endsection
