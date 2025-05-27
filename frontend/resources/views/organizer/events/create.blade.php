@extends('components.layout')

@section('title', 'Create New Event')

@section('content')
    @include('components.navbar')

    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create New Event</h1>
                <p class="mt-2 text-sm text-gray-600">Fill out the form below to create a new event</p>
            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-5 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <h2 class="text-xl font-semibold text-white">Event Details</h2>
                </div>

                <div class="p-6 space-y-8">
                    @if ($errors->any())
                        <div class="p-4 rounded-md bg-red-50 border-l-4 border-red-500">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">
                                        There were {{ $errors->count() }} errors with your submission
                                    </h3>
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

                    <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data"
                        class="space-y-6 divide-y divide-gray-200">
                        @csrf

                        <div class="space-y-6 pt-6">
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">

                                <!-- Event Name -->
                                <div class="sm:col-span-6">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Event Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="sm:col-span-6">
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="description" name="description" rows="4"
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">{{ old('description') }}</textarea>
                                    <p class="mt-2 text-sm text-gray-500">Write a few sentences about your event.</p>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Start Time -->
                                <div class="sm:col-span-3">
                                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Date &
                                        Time <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="start_time" name="start_time"
                                        value="{{ old('start_time') }}" required
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                    @error('start_time')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- End Time -->
                                <div class="sm:col-span-3">
                                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Date & Time
                                        <span class="text-red-500">*</span></label>
                                    <input type="datetime-local" id="end_time" name="end_time"
                                        value="{{ old('end_time') }}" required
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                    @error('end_time')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Location -->
                                <div class="sm:col-span-4">
                                    <label for="location" class="block text-sm font-medium text-gray-700">Location <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                                        required
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                    @error('location')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Speaker -->
                                <div class="sm:col-span-4">
                                    <label for="speaker" class="block text-sm font-medium text-gray-700">Speaker</label>
                                    <input type="text" id="speaker" name="speaker" value="{{ old('speaker') }}"
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                    @error('speaker')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Registration Fee -->
                                <div class="sm:col-span-3">
                                    <label for="registration_fee"
                                        class="block text-sm font-medium text-gray-700">Registration Fee
                                        ($)</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" id="registration_fee"
                                            name="registration_fee" value="{{ old('registration_fee', 0) }}"
                                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm rounded-md py-3 px-4">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">USD</span>
                                        </div>
                                    </div>
                                    @error('registration_fee')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Max Participants -->
                                <div class="sm:col-span-3">
                                    <label for="max_participants" class="block text-sm font-medium text-gray-700">Max
                                        Participants</label>
                                    <input type="number" min="1" id="max_participants" name="max_participants"
                                        value="{{ old('max_participants') }}"
                                        class="mt-1 block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm py-3 px-4">
                                    <p class="mt-2 text-sm text-gray-500">Leave empty for unlimited participants.</p>
                                    @error('max_participants')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Poster Upload -->
                                <div class="sm:col-span-6">
                                    <label for="poster" class="block text-sm font-medium text-gray-700">Event
                                        Poster</label>
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>

                                            <!-- Preview Container -->
                                            <div id="poster-preview-container" class="hidden">
                                                <img id="poster-preview"
                                                    class="max-h-40 mx-auto rounded border border-gray-200" src=""
                                                    alt="Poster Preview" />
                                            </div>

                                            <div class="flex text-sm text-gray-600">
                                                <label for="poster"
                                                    class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Upload a file</span>
                                                    <input id="poster" name="poster" type="file" accept="image/*"
                                                        class="sr-only">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                        </div>
                                    </div>
                                    @error('poster')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="pt-6">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('organizer.events.index') }}"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </a>
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Create Event
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Image Preview -->
    <script>
        document.getElementById('poster').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('poster-preview-container');
            const previewImage = document.getElementById('poster-preview');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
                previewImage.src = '';
            }
        });
    </script>
@endsection
