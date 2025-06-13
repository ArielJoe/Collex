@extends('components.layout')

@section('title')
    {{ $event['name'] ?? 'Event Details' }}
@endsection

@section('content')
    @include('components.navbar')
    <div class="container mx-auto px-4 py-8">
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-md" role="alert">
                <div class="flex justify-between items-center">
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                    <button type="button" class="text-red-700 hover:text-red-900">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $event['name'] ?? 'Untitled Event' }}</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('organizer.events.edit', $event['_id'] ?? $event['id']) }}"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-5 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                    </path>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('organizer.events.destroy', $event['_id'] ?? $event['id']) }}"
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this event?')"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-5 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-700 flex items-center mb-2">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Start Date & Time
                                </h3>
                                <p class="text-gray-600">
                                    {{ isset($event['start_time']) ? \Carbon\Carbon::parse($event['start_time'])->format('l, F j, Y \a\t g:i A') : 'Not specified' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-700 flex items-center mb-2">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    End Date & Time
                                </h3>
                                <p class="text-gray-600">
                                    {{ isset($event['end_time']) ? \Carbon\Carbon::parse($event['end_time'])->format('l, F j, Y \a\t g:i A') : 'Not specified' }}
                                </p>
                            </div>
                        </div>

                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-700 flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Location
                            </h3>
                            <p class="text-gray-600">{{ $event['location'] ?? 'Not specified' }}</p>
                        </div>

                        @if (!empty($event['poster_url']))
                            <div class="mb-6">
                                <h5 class="text-lg font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Event Poster
                                </h5>
                                <div class="relative overflow-hidden rounded-lg shadow-md bg-gray-50">
                                    <img src="{{ $event['poster_url'] }}" alt="Event Poster"
                                        class="w-full h-auto max-h-96 object-cover" loading="lazy">
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-800">Event Information</h3>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-4">
                            <li>
                                <h4 class="text-sm font-medium text-gray-500">Event ID</h4>
                                <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 p-2 rounded">
                                    {{ $event['_id'] ?? ($event['id'] ?? 'N/A') }}
                                </p>
                            </li>
                            <li>
                                <h4 class="text-sm font-medium text-gray-500">Organizer ID</h4>
                                <p class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 p-2 rounded">
                                    {{ $event['organizer_id'] ?? 'N/A' }}
                                </p>
                            </li>
                            @if (isset($event['created_at']))
                                <li>
                                    <h4 class="text-sm font-medium text-gray-500">Created</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($event['created_at'])->format('M j, Y g:i A') }}
                                    </p>
                                </li>
                            @endif
                            @if (isset($event['updated_at']))
                                <li>
                                    <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($event['updated_at'])->format('M j, Y g:i A') }}
                                    </p>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('organizer.events.index') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                Back to Events
            </a>
        </div>
    </div>
    @include('components.footer')
@endsection
