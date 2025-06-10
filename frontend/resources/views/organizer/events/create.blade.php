@extends('components.layout')

@section('title')
    Create Event
@endsection

@section('content')
    @include('components.navbar')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6">
            <header class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Create New Event</h1>
                <p class="text-gray-600 mt-1">Fill in the details below to create your event</p>
            </header>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Error Message -->
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your
                                submission</h3>
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

            <!-- General Server Error or API Error -->
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded" role="alert">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('organizer.events.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                <div class="space-y-6">
                    <!-- Basic Information Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                        <p class="mt-1 text-sm text-gray-500">General details about your event.</p>

                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-6">
                                <label for="name" class="block text-sm font-medium text-gray-700">Event Name</label>
                                <div class="mt-1">
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                <div class="mt-1">
                                    <textarea id="description" name="description" rows="3"
                                        class="block w-full rounded-md border shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Write a few sentences about your event.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <div class="mt-1">
                                    <input type="text" name="location" id="location" value="{{ old('location') }}"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('location') border-red-500 @enderror">
                                    @error('location')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="faculty" class="block text-sm font-medium text-gray-700">Faculty</label>
                                <div class="mt-1">
                                    <select id="faculty" name="faculty"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('faculty') border-red-500 @enderror">
                                        <option value="">Select Faculty</option>
                                        <option value="FK" {{ old('faculty') == 'FK' ? 'selected' : '' }}>Fakultas
                                            Kedokteran (FK)</option>
                                        <option value="FKG" {{ old('faculty') == 'FKG' ? 'selected' : '' }}>Fakultas
                                            Kedokteran Gigi (FKG)</option>
                                        <option value="FP" {{ old('faculty') == 'FP' ? 'selected' : '' }}>Fakultas
                                            Psikologi (FP)</option>
                                        <option value="FTRC" {{ old('faculty') == 'FTRC' ? 'selected' : '' }}>Fakultas
                                            Teknologi dan Rekayasa Cerdas (FTRC)</option>
                                        <option value="FHIK" {{ old('faculty') == 'FHIK' ? 'selected' : '' }}>Fakultas
                                            Humaniora dan Industri Kreatif (FHIK)</option>
                                        <option value="FHBD" {{ old('faculty') == 'FHBD' ? 'selected' : '' }}>Fakultas
                                            Hukum dan Bisnis Digital (FHBD)</option>
                                    </select>
                                    @error('faculty')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="max_participant" class="block text-sm font-medium text-gray-700">Max
                                    Participant</label>
                                <div class="mt-1">
                                    <input type="number" name="max_participant" id="max_participant"
                                        value="{{ old('max_participant') }}"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('max_participant') border-red-500 @enderror">
                                    @error('max_participant')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="poster" class="block text-sm font-medium text-gray-700">Poster Image</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" name="poster" id="poster" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 @error('poster') border-red-500 @enderror">
                                    @error('poster')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg font-medium text-gray-900">Date & Time</h2>
                        <p class="mt-1 text-sm text-gray-500">When will your event take place?</p>

                        <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="registration_deadline"
                                    class="block text-sm font-medium text-gray-700">Registration Deadline</label>
                                <div class="mt-1">
                                    <input type="datetime-local" name="registration_deadline" id="registration_deadline"
                                        value="{{ old('registration_deadline') }}"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('registration_deadline') border-red-500 @enderror">
                                    @error('registration_deadline')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <div class="mt-1">
                                    <input type="datetime-local" name="start_time" id="start_time"
                                        value="{{ old('start_time') }}"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('start_time') border-red-500 @enderror">
                                    @error('start_time')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                                <div class="mt-1">
                                    <input type="datetime-local" name="end_time" id="end_time"
                                        value="{{ old('end_time') }}"
                                        class="block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-3 border @error('end_time') border-red-500 @enderror">
                                    @error('end_time')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Details Section -->
                    <div id="details-container">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">Event Details</h2>
                                <p class="mt-1 text-sm text-gray-500">Add specific sessions or activities for your event.
                                </p>
                            </div>
                            <button type="button" id="add-detail"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Session
                            </button>
                        </div>

                        <div id="details-list" class="mt-6 space-y-4">
                            @if (old('details'))
                                @foreach (old('details') as $index => $detail)
                                    <div class="detail-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                                            <div class="sm:col-span-6">
                                                <label for="details[{{ $index }}][title]"
                                                    class="block text-sm font-medium text-gray-700">Session Title</label>
                                                <input type="text" name="details[{{ $index }}][title]"
                                                    value="{{ $detail['title'] }}"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.title') border-red-500 @enderror">
                                                @error('details.' . $index . '.title')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label for="details[{{ $index }}][start_time]"
                                                    class="block text-sm font-medium text-gray-700">Start Time</label>
                                                <input type="datetime-local"
                                                    name="details[{{ $index }}][start_time]"
                                                    value="{{ $detail['start_time'] }}"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.start_time') border-red-500 @enderror">
                                                @error('details.' . $index . '.start_time')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label for="details[{{ $index }}][end_time]"
                                                    class="block text-sm font-medium text-gray-700">End Time</label>
                                                <input type="datetime-local"
                                                    name="details[{{ $index }}][end_time]"
                                                    value="{{ $detail['end_time'] }}"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.end_time') border-red-500 @enderror">
                                                @error('details.' . $index . '.end_time')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-2">
                                                <label for="details[{{ $index }}][location]"
                                                    class="block text-sm font-medium text-gray-700">Location</label>
                                                <input type="text" name="details[{{ $index }}][location]"
                                                    value="{{ $detail['location'] }}"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.location') border-red-500 @enderror">
                                                @error('details.' . $index . '.location')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="details[{{ $index }}][speaker]"
                                                    class="block text-sm font-medium text-gray-700">Speaker</label>
                                                <input type="text" name="details[{{ $index }}][speaker]"
                                                    value="{{ $detail['speaker'] }}"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.speaker') border-red-500 @enderror">
                                                @error('details.' . $index . '.speaker')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="details[{{ $index }}][price]"
                                                    class="block text-sm font-medium text-gray-700">Price (IDR)</label>
                                                <input type="number" step="0.01"
                                                    name="details[{{ $index }}][price]"
                                                    value="{{ $detail['price'] }}"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.price') border-red-500 @enderror">
                                                @error('details.' . $index . '.price')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-6">
                                                <label for="details[{{ $index }}][description]"
                                                    class="block text-sm font-medium text-gray-700">Description</label>
                                                <textarea name="details[{{ $index }}][description]" rows="2"
                                                    class="mt-1 block w-full rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border @error('details.' . $index . '.description') border-red-500 @enderror">{{ $detail['description'] }}</textarea>
                                                @error('details.' . $index . '.description')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mt-3 flex justify-end">
                                            <button type="button"
                                                class="remove-detail inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Remove Session
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <div class="flex justify-end">
                        <button type="submit"
                            class="ml-3 inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Create Event
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addButton = document.getElementById('add-detail');
            const detailsList = document.getElementById('details-list');
            let detailIndex = {{ old('details', [null])[0] ? count(old('details')) : 0 }};

            if (!addButton || !detailsList) {
                console.error('Add button or details list not found in DOM. Check IDs: add-detail, details-list');
                return;
            }

            addButton.addEventListener('click', () => {
                const detailDiv = document.createElement('div');
                detailDiv.className = 'detail-row bg-gray-50 p-4 rounded-lg border border-gray-200';
                detailDiv.innerHTML = `
                    <div class="grid grid-cols-1 gap-y-4 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-6">
                            <label for="details[${detailIndex}][title]" class="block text-sm font-medium text-gray-700">Session Title</label>
                            <input type="text" name="details[${detailIndex}][title]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="details[${detailIndex}][start_time]" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="datetime-local" name="details[${detailIndex}][start_time]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="details[${detailIndex}][end_time]" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" name="details[${detailIndex}][end_time]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="details[${detailIndex}][location]" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" name="details[${detailIndex}][location]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="details[${detailIndex}][speaker]" class="block text-sm font-medium text-gray-700">Speaker</label>
                            <input type="text" name="details[${detailIndex}][speaker]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="details[${detailIndex}][price]" class="block text-sm font-medium text-gray-700">Price (IDR)</label>
                            <input type="number" step="0.01" name="details[${detailIndex}][price]" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border">
                        </div>

                        <div class="sm:col-span-6">
                            <label for="details[${detailIndex}][description]" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="details[${detailIndex}][description]" rows="2" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm p-2 border"></textarea>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="button" class="remove-detail inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Remove Session
                        </button>
                    </div>
                `;
                detailsList.appendChild(detailDiv);
                detailIndex++;

                // Add remove functionality
                detailDiv.querySelector('.remove-detail').addEventListener('click', () => {
                    detailDiv.remove();
                });
            });

            // Add remove functionality to existing rows
            document.querySelectorAll('.remove-detail').forEach(button => {
                button.addEventListener('click', () => {
                    button.closest('.detail-row').remove();
                });
            });
        });
    </script>
@endpush
