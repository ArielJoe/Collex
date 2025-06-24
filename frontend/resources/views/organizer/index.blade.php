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
                    <button id="quick-actions-button"
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span>Quick Actions</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div id="quick-actions-dropdown"
                        class="absolute right-0 mt-2 w-56 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-10 hidden">
                        <div class="py-1">
                            <a href="{{ route('organizer.events.create') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-plus mr-2 text-blue-500"></i>Create Event
                            </a>
                            <a href="{{ route('organizer.events.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-calendar-alt mr-2 text-green-500"></i>View All Events
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-chart-bar mr-2 text-purple-500"></i>View Reports
                            </a>
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
                                <i class="fas fa-calendar text-blue-500 text-2xl"></i>
                            </div>
                        </div>
                        <a href="{{ route('organizer.events.index') }}"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            View all events
                            <i class="fas fa-chevron-right ml-1 text-sm"></i>
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
                                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                            </div>
                        </div>
                        <a href="#"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            View upcoming
                            <i class="fas fa-chevron-right ml-1 text-sm"></i>
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
                                <i class="fas fa-users text-green-500 text-2xl"></i>
                            </div>
                        </div>
                        <a href="#"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            View analytics
                            <i class="fas fa-chevron-right ml-1 text-sm"></i>
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
                                <i class="fas fa-dollar-sign text-cyan-500 text-2xl"></i>
                            </div>
                        </div>
                        <a href="#"
                            class="mt-4 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            View reports
                            <i class="fas fa-chevron-right ml-1 text-sm"></i>
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
                                <i class="fas fa-plus text-blue-500 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Create New Event</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Set up a new event, configure tickets, and start promoting to your
                            audience.</p>
                        <a href="{{ route('organizer.events.create') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create Event
                        </a>
                    </div>
                </div>

                <!-- Manage Events -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="p-3 rounded-lg bg-green-50 mr-4">
                                <i class="fas fa-tasks text-green-500 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900">Manage Events</h3>
                        </div>
                        <p class="text-gray-600 mb-6">View, edit, and manage all your current and past events in one place.
                        </p>
                        <a href="{{ route('organizer.events.index') }}"
                            class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <i class="fas fa-calendar-alt mr-2"></i>
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
                    <div class="table-responsive">
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
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">Tech Conference 2023</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">Nov 15, 2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">San Francisco, CA</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">450</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2 justify-end">
                                            <a href="#"
                                                class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                                class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors duration-200">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50 transition-colors duration-200">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">Music Festival</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">Dec 5, 2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">New York, NY</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">1,200</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2 justify-end">
                                            <a href="#"
                                                class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                                class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors duration-200">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50 transition-colors duration-200">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">Business Workshop</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">Oct 20, 2023</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">Chicago, IL</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">85</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-medium rounded-full bg-blue-100 text-blue-800">Completed</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2 justify-end">
                                            <a href="#"
                                                class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="#"
                                                class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors duration-200">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#"
                                                class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50 transition-colors duration-200">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 text-center">
                    <a href="{{ route('organizer.events.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        View All Events
                        <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Scan QR Button -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Scan QR for Attendance</h3>
                </div>
                <div class="p-6 text-center">
                    <button id="open-qr-modal"
                        class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-qrcode mr-2"></i>
                        Open QR Scanner
                    </button>
                </div>
            </div>

            <!-- QR Scanner Modal -->
            <div id="qr-modal"
                class="fixed inset-0 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4 hidden">
                <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl">
                    <div class="flex justify-between items-center p-4">
                        <h3 class="text-lg font-medium text-gray-900">Scan QR Code</h3>
                        <button id="close-qr-modal"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <div id="reader" style="width: 100%; height: 300px;"
                            class="border border-gray-200 rounded-md mb-4"></div>
                        <p class="text-sm font-medium">Result: <span id="qr-result"
                                class="text-gray-700 font-normal"></span></p>
                        <p id="qr-error" class="text-red-600 text-sm mt-2 hidden"></p>
                    </div>
                    <div class="p-4 flex justify-center space-x-4">
                        <button id="start-scan"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <i class="fas fa-play mr-2"></i>Start Scan
                        </button>
                        <button id="stop-scan"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 hidden">
                            <i class="fas fa-stop mr-2"></i>Stop Scan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Success Modal -->
            <div id="success-modal"
                class="fixed inset-0 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4 hidden">
                <div class="relative w-full max-w-md bg-white rounded-lg shadow-xl">
                    <div class="flex justify-between items-center p-4">
                        <h3 class="text-lg font-medium text-gray-900">Attendance Confirmed</h3>
                        <button id="close-success-modal"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="p-4 text-center">
                        <p class="text-green-600">Attendance has been successfully confirmed!</p>
                        <p class="mt-2"><strong>Event:</strong> <span id="success-event-name"></span></p>
                    </div>
                    <div class="p-4 flex justify-center">
                        <button id="close-success-modal-btn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')

    @push('style')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            @media (max-width: 640px) {
                .table-responsive {
                    min-width: 600px;
                }
            }

            .transition-colors {
                transition-property: background-color, border-color, color, fill, stroke;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 200ms;
            }
        </style>
    @endpush

    @push('script')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // QR Scanner elements
                const openModalButton = document.getElementById('open-qr-modal');
                const closeModalButton = document.getElementById('close-qr-modal');
                const qrModal = document.getElementById('qr-modal');
                const qrResult = document.getElementById('qr-result');
                const qrError = document.getElementById('qr-error');
                const startButton = document.getElementById('start-scan');
                const stopButton = document.getElementById('stop-scan');
                const successModal = document.getElementById('success-modal');
                const successEventName = document.getElementById('success-event-name');

                let html5QrCode = null;

                function showError(message) {
                    qrError.textContent = message;
                    qrError.classList.remove('hidden');
                    console.error('QR Error:', message);
                }

                function clearError() {
                    qrError.textContent = '';
                    qrError.classList.add('hidden');
                }

                function showSuccess(message, eventName = '') {
                    successEventName.textContent = eventName;
                    qrResult.innerHTML = `<span class="text-green-600">${message}</span>`;
                    successModal.classList.remove('hidden');
                    console.log('QR Success:', message);
                }

                async function onScanSuccess(decodedText, decodedResult) {
                    console.log('QR Code Scanned:', decodedText);
                    qrResult.innerHTML = 'Processing... <i class="fas fa-spinner fa-spin ml-1"></i>';
                    clearError();

                    try {
                        // Parse the QR code content (assuming format: "registration_id:detail_id")
                        const [registrationId, detailId] = decodedText.split(':');

                        if (!registrationId || !detailId) {
                            throw new Error('Invalid QR code format');
                        }

                        // Call Node.js backend API
                        const response = await fetch('http://localhost:5000/api/attendance/scan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                qr_code: decodedText,
                                registration_id: registrationId,
                                event_detail_id: detailId
                            })
                        });

                        console.log('API Response Status:', response.status);

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw new Error(errorData.message || 'Failed to verify attendance');
                        }

                        const data = await response.json();
                        console.log('API Response Data:', data);

                        // Show success with actual data from API
                        showSuccess('Attendance confirmed!', data.data?.event_name || 'Event');
                        stopScanner();

                    } catch (error) {
                        console.error('Scan processing error:', error);
                        showError(error.message);
                        stopScanner();
                    }
                }

                function onScanError(error) {
                    if (error && error.includes('No MultiFormat Readers')) {
                        showError('QR code not recognized. Please try again.');
                    } else {
                        showError('Scan error: ' + (error || 'Unknown error'));
                    }
                }

                function startScanner() {
                    if (!html5QrCode) {
                        html5QrCode = new Html5Qrcode('reader');
                    }

                    Html5Qrcode.getCameras().then(devices => {
                        if (devices && devices.length) {
                            const cameraId = devices[0].id;
                            const config = {
                                fps: 10,
                                qrbox: {
                                    width: 250,
                                    height: 250
                                },
                                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                            };

                            html5QrCode.start(
                                cameraId,
                                config,
                                onScanSuccess,
                                onScanError
                            ).then(() => {
                                startButton.classList.add('hidden');
                                stopButton.classList.remove('hidden');
                                clearError();
                                qrResult.textContent = 'Scanning...';
                            }).catch(err => {
                                showError('Failed to start scanner: ' + err);
                            });
                        } else {
                            showError('No cameras found on this device.');
                        }
                    }).catch(err => {
                        showError('Camera access denied or unavailable: ' + err);
                    });
                }

                function stopScanner() {
                    if (html5QrCode) {
                        html5QrCode.stop().then(() => {
                            html5QrCode.clear();
                            startButton.classList.remove('hidden');
                            stopButton.classList.add('hidden');
                        }).catch(err => {
                            console.error('Error stopping scanner:', err);
                        });
                    }
                }

                // Event listeners
                openModalButton.addEventListener('click', () => {
                    qrModal.classList.remove('hidden');
                    qrResult.textContent = 'Ready to scan';
                    clearError();
                });

                closeModalButton.addEventListener('click', () => {
                    qrModal.classList.add('hidden');
                    stopScanner();
                });

                startButton.addEventListener('click', startScanner);
                stopButton.addEventListener('click', stopScanner);

                // Close modals when clicking outside or pressing ESC
                [qrModal, successModal].forEach(modal => {
                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) {
                            modal.classList.add('hidden');
                            if (modal === qrModal) stopScanner();
                        }
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        qrModal.classList.add('hidden');
                        successModal.classList.add('hidden');
                        stopScanner();
                    }
                });
            });
        </script>
    @endpush
@endsection
