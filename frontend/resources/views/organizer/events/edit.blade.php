@extends('components.layout')

@section('title', 'Edit Event | ' . ($event['data']['name'] ?? 'Event'))

@section('content')
    @include('components.navbar')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div class="px-6 py-5 sm:px-8 sm:py-6">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold">Edit Event: {{ $event['data']['name'] ?? 'Untitled' }}</h1>
                            <a href="{{ route('organizer.events.show', $event['data']['_id'] ?? $event['data']['id']) }}"
                                class="flex items-center text-sm font-medium text-white hover:text-blue-100 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Event
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6 sm:p-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">There were {{ count($errors) }} errors with
                                        your submission</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('organizer.events.update', $event['data']['_id'] ?? $event['data']['id']) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Event Basic Information -->
                        <div class="mb-8">
                            <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-4">Event
                                Information</h2>

                            <!-- Event Name -->
                            <div class="mb-6">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Event Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                    value="{{ old('name', $event['data']['name'] ?? '') }}">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date & Time -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Date
                                        &
                                        Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="start_time" name="start_time" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_time') border-red-500 @enderror"
                                        value="{{ old('start_time', isset($event['data']['start_time']) ? \Carbon\Carbon::parse($event['data']['start_time'])->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '') }}">
                                    @error('start_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Date &
                                        Time
                                        <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="end_time" name="end_time" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_time') border-red-500 @enderror"
                                        value="{{ old('end_time', isset($event['data']['end_time']) ? \Carbon\Carbon::parse($event['data']['end_time'])->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '') }}">
                                    @error('end_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="mb-6">
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location <span
                                        class="text-red-500">*</span></label>
                                <input type="text" id="location" name="location" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror"
                                    value="{{ old('location', $event['data']['location'] ?? '') }}">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Poster Upload -->
                            <div class="mb-6">
                                <label for="poster" class="block text-sm font-medium text-gray-700 mb-1">Event
                                    Poster</label>

                                @if (!empty($event['data']['poster_url']))
                                    <div class="mb-4">
                                        <div class="relative max-w-xs">
                                            <img src="{{ $event['data']['poster_url'] }}" alt="Current Poster"
                                                class="rounded-lg border border-gray-200 shadow-sm w-full h-auto">
                                            <div
                                                class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity rounded-lg">
                                                <span
                                                    class="text-white text-sm font-medium bg-black bg-opacity-50 px-2 py-1 rounded">Current
                                                    Poster</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Upload a new image to replace this poster</p>
                                    </div>
                                @endif

                                <div class="flex items-center">
                                    <label for="poster" class="cursor-pointer">
                                        <div
                                            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Choose File
                                        </div>
                                        <input id="poster" name="poster" type="file" class="sr-only"
                                            accept="image/*">
                                    </label>
                                    <span id="file-name" class="ml-3 text-sm text-gray-500">No file chosen</span>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Accepted: JPG, PNG, GIF. Max: 2MB</p>
                                @error('poster')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Event Sessions -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2">Event Sessions
                                </h2>
                                <button type="button" onclick="addSession()"
                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    + Add Session
                                </button>
                            </div>

                            <div id="sessions-container">
                                @foreach ($event['data']['details'] as $index => $detail)
                                    <div class="session-card mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                                        <div class="flex justify-between items-center mb-3">
                                            <h3 class="font-medium text-gray-700">Session {{ $index + 1 }}</h3>
                                            <button type="button" onclick="removeSession(this)"
                                                class="text-red-500 hover:text-red-700 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>

                                        <input type="hidden" name="sessions[{{ $index }}][id]"
                                            value="{{ $detail['_id'] }}">

                                        <!-- Session Title -->
                                        <div class="mb-4">
                                            <label for="session_title_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="session_title_{{ $index }}"
                                                name="sessions[{{ $index }}][title]" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old("sessions.$index.title", $detail['title'] ?? '') }}">
                                        </div>

                                        <!-- Session Time -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label for="session_start_time_{{ $index }}"
                                                    class="block text-sm font-medium text-gray-700 mb-1">Start Time <span
                                                        class="text-red-500">*</span></label>
                                                <input type="datetime-local" id="session_start_time_{{ $index }}"
                                                    name="sessions[{{ $index }}][start_time]" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    value="{{ old("sessions.$index.start_time", isset($detail['start_time']) ? \Carbon\Carbon::parse($detail['start_time'])->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '') }}">
                                            </div>
                                            <div>
                                                <label for="session_end_time_{{ $index }}"
                                                    class="block text-sm font-medium text-gray-700 mb-1">End Time <span
                                                        class="text-red-500">*</span></label>
                                                <input type="datetime-local" id="session_end_time_{{ $index }}"
                                                    name="sessions[{{ $index }}][end_time]" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    value="{{ old("sessions.$index.end_time", isset($detail['end_time']) ? \Carbon\Carbon::parse($detail['end_time'])->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '') }}">
                                            </div>
                                        </div>

                                        <!-- Session Location -->
                                        <div class="mb-4">
                                            <label for="session_location_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 mb-1">Location <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="session_location_{{ $index }}"
                                                name="sessions[{{ $index }}][location]" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old("sessions.$index.location", $detail['location'] ?? '') }}">
                                        </div>

                                        <!-- Session Speaker -->
                                        <div class="mb-4">
                                            <label for="session_speaker_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 mb-1">Speaker <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" id="session_speaker_{{ $index }}"
                                                name="sessions[{{ $index }}][speaker]" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old("sessions.$index.speaker", $detail['speaker'] ?? '') }}">
                                        </div>

                                        <!-- Session Price -->
                                        <div class="mb-4">
                                            <label for="session_price_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 mb-1">Price (IDR)</label>
                                            <input type="number" id="session_price_{{ $index }}"
                                                name="sessions[{{ $index }}][price]"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                value="{{ old("sessions.$index.price", $detail['price'] ?? '') }}">
                                        </div>

                                        <!-- Session Description -->
                                        <div class="mb-4">
                                            <label for="session_description_{{ $index }}"
                                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea id="session_description_{{ $index }}" name="sessions[{{ $index }}][description]"
                                                rows="3"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old("sessions.$index.description", $detail['description'] ?? '') }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('organizer.events.show', $event['data']['_id'] ?? $event['data']['id']) }}"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Event
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('components.footer')

    <script>
        // Show selected file name
        document.getElementById('poster').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            document.getElementById('file-name').textContent = fileName;
        });

        // Session management
        let sessionCount = {{ count($event['data']['details']) }};

        function addSession() {
            const container = document.getElementById('sessions-container');
            const newSession = document.createElement('div');
            newSession.className = 'session-card mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50';
            newSession.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-medium text-gray-700">New Session</h3>
                    <button type="button" onclick="removeSession(this)"
                        class="text-red-500 hover:text-red-700 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>

                <input type="hidden" name="sessions[${sessionCount}][id]" value="new">

                <!-- Session Title -->
                <div class="mb-4">
                    <label for="session_title_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">Title <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="session_title_${sessionCount}" name="sessions[${sessionCount}][title]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Session Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="session_start_time_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span
                                class="text-red-500">*</span></label>
                        <input type="datetime-local" id="session_start_time_${sessionCount}" name="sessions[${sessionCount}][start_time]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="session_end_time_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">End Time <span
                                class="text-red-500">*</span></label>
                        <input type="datetime-local" id="session_end_time_${sessionCount}" name="sessions[${sessionCount}][end_time]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Session Location -->
                <div class="mb-4">
                    <label for="session_location_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">Location <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="session_location_${sessionCount}" name="sessions[${sessionCount}][location]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Session Speaker -->
                <div class="mb-4">
                    <label for="session_speaker_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">Speaker <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="session_speaker_${sessionCount}" name="sessions[${sessionCount}][speaker]" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Session Price -->
                <div class="mb-4">
                    <label for="session_price_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">Price (IDR)</label>
                    <input type="number" id="session_price_${sessionCount}" name="sessions[${sessionCount}][price]"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Session Description -->
                <div class="mb-4">
                    <label for="session_description_${sessionCount}" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="session_description_${sessionCount}" name="sessions[${sessionCount}][description]" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            `;
            container.appendChild(newSession);
            sessionCount++;
        }

        function removeSession(button) {
            if (confirm('Are you sure you want to remove this session?')) {
                const sessionCard = button.closest('.session-card');
                sessionCard.remove();
            }
        }
    </script>
@endsection
