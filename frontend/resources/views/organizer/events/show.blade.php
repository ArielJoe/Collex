@extends('components.layout')

@section('title')
    {{ $event['data']['name'] ?? 'Event Details' }}
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
                    <button type="button" class="text-red-700 hover:text-red-900"
                        onclick="this.parentElement.parentElement.style.display='none'">
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
                        <h2 class="text-2xl font-bold text-gray-800">{{ $event['data']['name'] ?? 'Untitled Event' }}</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('organizer.events.edit', $event['data']['_id']) }}"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-5 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="-ml-1 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                    </path>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('organizer.events.destroy', $event['data']['_id']) }}" method="POST"
                                class="inline">
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
                        <!-- Event Poster -->
                        @if (!empty($event['data']['poster_url']))
                            <div class="mb-6">
                                <div class="relative overflow-hidden rounded-lg shadow-md bg-gray-50">
                                    <img src="{{ $event['data']['poster_url'] }}" alt="Event Poster"
                                        class="w-full h-auto max-h-96 object-contain mx-auto" loading="lazy">
                                </div>
                            </div>
                        @endif

                        <!-- Event Details Grid -->
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
                                    {{ isset($event['data']['start_time']) ? \Carbon\Carbon::parse($event['data']['start_time'])->format('l, F j, Y \a\t g:i A') : 'Not specified' }}
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
                                    {{ isset($event['data']['end_time']) ? \Carbon\Carbon::parse($event['data']['end_time'])->format('l, F j, Y \a\t g:i A') : 'Not specified' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-700 flex items-center mb-2">
                                    <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Registration Deadline
                                </h3>
                                <p class="text-gray-600">
                                    {{ isset($event['data']['registration_deadline']) ? \Carbon\Carbon::parse($event['data']['registration_deadline'])->format('l, F j, Y \a\t g:i A') : 'Not specified' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-700 flex items-center mb-2">
                                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Max Participants
                                </h3>
                                <p class="text-gray-600">
                                    {{ $event['data']['max_participant'] ?? 'Not specified' }}
                                </p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-700 flex items-center mb-2">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Location
                            </h3>
                            <p class="text-gray-600">{{ $event['data']['location'] ?? 'Not specified' }}</p>
                        </div>

                        <!-- Event Details Sections -->
                        @if (isset($event['data']['details']) && count($event['data']['details']) > 0)
                            <div class="mt-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-4">Event Details</h3>
                                <div class="space-y-6">
                                    @foreach ($event['data']['details'] as $detail)
                                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                                <h4 class="text-lg font-semibold text-gray-800">{{ $detail['title'] }}</h4>
                                            </div>
                                            <div class="p-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <h5 class="text-sm font-medium text-gray-500">Time</h5>
                                                        <p class="text-gray-600">
                                                            {{ \Carbon\Carbon::parse($detail['start_time'])->format('g:i A') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($detail['end_time'])->format('g:i A, l, F j, Y') }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <h5 class="text-sm font-medium text-gray-500">Location</h5>
                                                        <p class="text-gray-600">{{ $detail['location'] }}</p>
                                                    </div>
                                                </div>

                                                @if (!empty($detail['speaker']))
                                                    <div class="mb-4">
                                                        <h5 class="text-sm font-medium text-gray-500">Speaker</h5>
                                                        <p class="text-gray-600">{{ $detail['speaker'] }}</p>
                                                    </div>
                                                @endif

                                                @if (!empty($detail['price']))
                                                    <div class="mb-4">
                                                        <h5 class="text-sm font-medium text-gray-500">Price</h5>
                                                        <p class="text-gray-600">Rp
                                                            {{ number_format($detail['price'], 0, ',', '.') }}</p>
                                                    </div>
                                                @endif

                                                @if (!empty($detail['description']))
                                                    <div>
                                                        <h5 class="text-sm font-medium text-gray-500">Description</h5>
                                                        <div class="prose max-w-none text-gray-600">
                                                            {!! nl2br(e($detail['description'])) !!}
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
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
                                    {{ $event['data']['_id'] ?? 'N/A' }}
                                </p>
                            </li>
                            <li>
                                <h4 class="text-sm font-medium text-gray-500">Organizer</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $event['data']['organizer']['email'] ?? 'N/A' }}
                                </p>
                            </li>
                            <li>
                                <h4 class="text-sm font-medium text-gray-500">Faculty</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $event['data']['faculty']['name'] ?? 'N/A' }}
                                    @if (isset($event['data']['faculty']['code']))
                                        <span class="text-gray-500">({{ $event['data']['faculty']['code'] }})</span>
                                    @endif
                                </p>
                            </li>
                            @if (isset($event['data']['created_at']))
                                <li>
                                    <h4 class="text-sm font-medium text-gray-500">Created</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($event['data']['created_at'])->format('M j, Y g:i A') }}
                                    </p>
                                </li>
                            @endif
                            @if (isset($event['data']['updated_at']))
                                <li>
                                    <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($event['data']['updated_at'])->format('M j, Y g:i A') }}
                                    </p>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Registration Status -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mt-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h3 class="text-lg font-medium text-blue-800">Registration Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-gray-700">Registered Participants</span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $event['data']['registered_participant'] ?? 0 }} /
                                    {{ $event['data']['max_participant'] ?? '∞' }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full"
                                    style="width: {{ isset($event['data']['max_participant']) && $event['data']['max_participant'] > 0 ? min(100, (($event['data']['registered_participant'] ?? 0) / $event['data']['max_participant']) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>

                        @php
                            $now = now();
                            $deadline = isset($event['data']['registration_deadline'])
                                ? \Carbon\Carbon::parse($event['data']['registration_deadline'])
                                : null;
                        @endphp

                        <div class="mt-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if ($deadline && $now->gt($deadline)) bg-red-100 text-red-800
                                @else
                                    bg-green-100 text-green-800 @endif">
                                @if ($deadline && $now->gt($deadline))
                                    Registration Closed
                                @else
                                    Registration Open
                                @endif
                            </span>
                        </div>

                        @if ($deadline)
                            <p class="mt-2 text-sm text-gray-600">
                                @if ($now->gt($deadline))
                                    Registration closed on {{ $deadline->format('M j, Y g:i A') }}
                                @else
                                    Registration closes in
                                    {{ $now->diffForHumans($deadline, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                                @endif
                            </p>
                        @endif
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
