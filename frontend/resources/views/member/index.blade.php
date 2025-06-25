@extends('components.layout')

@section('title')
    Member
@endsection

@section('content')
    @include('components.navbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-10">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">My Tickets</h1>
            <p class="mt-2 text-sm text-gray-600">View and manage your event tickets here.</p>
        </header>

        @if ($error)
            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-4 mb-8 rounded-md" role="alert"
                aria-live="polite">
                <p class="font-semibold">Error</p>
                <p>{{ $error }}</p>
            </div>
        @endif

        <div class="space-y-6">
            @forelse ($tickets as $event)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
                    <!-- Fixed size image container at top -->
                    <div class="w-full h-60"> <!-- Fixed height for all images -->
                        <img src="{{ $event['poster_url'] ?? 'https://via.placeholder.com/800x300.png?text=No+Image' }}"
                            alt="{{ $event['eventName'] }} Poster" class="w-full h-full object-cover">
                    </div>

                    <!-- Content below image -->
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $event['eventName'] }}</h2>

                        <div class="border-t border-gray-200 my-4"></div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <span class="block text-sm font-medium text-gray-500">Payment Status</span>
                                <p class="text-lg font-semibold">
                                    @php
                                        $status = $event['paymentStatus'] ?? 'unknown';
                                        $statusConfig = [
                                            'confirmed' => [
                                                'bg' => 'bg-emerald-100',
                                                'text' => 'text-emerald-800',
                                                'border' => 'border-emerald-200',
                                                'icon' => '✓',
                                            ],
                                            'pending' => [
                                                'bg' => 'bg-amber-100',
                                                'text' => 'text-amber-800',
                                                'border' => 'border-amber-200',
                                                'icon' => '⏳',
                                            ],
                                            'rejected' => [
                                                'bg' => 'bg-red-100',
                                                'text' => 'text-red-800',
                                                'border' => 'border-red-200',
                                                'icon' => '✗',
                                            ],
                                            'unknown' => [
                                                'bg' => 'bg-gray-100',
                                                'text' => 'text-gray-800',
                                                'border' => 'border-gray-200',
                                                'icon' => '?',
                                            ],
                                        ];
                                        $config = $statusConfig[$status] ?? $statusConfig['unknown'];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                        <span class="mr-1.5">{{ $config['icon'] }}</span>
                                        {{ ucfirst($status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="space-y-2">
                                <span class="block text-sm font-medium text-gray-500">Location</span>
                                <p class="text-lg text-gray-700">{{ $event['location'] }}</p>
                            </div>
                            <div class="space-y-2">
                                <span class="block text-sm font-medium text-gray-500">Event Starts</span>
                                <p class="text-lg text-gray-700">
                                    {{ \Carbon\Carbon::parse($event['start_time'])->format('D, M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <span class="block text-sm font-medium text-gray-500">Payment Confirmed</span>
                                <p class="text-lg text-gray-700">
                                    @if ($event['confirmedAt'])
                                        {{ \Carbon\Carbon::parse($event['confirmedAt'])->format('D, M j, Y') }}
                                    @else
                                        @if ($event['paymentStatus'] === 'rejected')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-md text-sm bg-red-50 text-red-700 border border-red-200">
                                                <span class="mr-1">✗</span>
                                                Payment Rejected
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-md text-sm bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                <span class="mr-1">⏳</span>
                                                Not confirmed yet
                                            </span>
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <details class="group" aria-expanded="false">
                                <summary
                                    class="flex justify-between items-center font-semibold text-gray-700 cursor-pointer py-3 bg-gray-50 rounded-lg px-4 transition-all duration-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        View Purchased Items
                                    </span>
                                    <span class="transition-transform duration-300 group-open:rotate-180 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </summary>
                                <div class="mt-3 space-y-2 p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    @forelse ($event['purchasedItems'] as $item)
                                        <div
                                            class="flex justify-between items-center p-3 bg-white rounded-md shadow-sm border border-gray-100">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                                <div>
                                                    <p class="text-md font-semibold text-gray-800">{{ $item['name'] }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">{{ $item['type'] }}</p>
                                                </div>
                                            </div>
                                            <p class="text-md font-semibold text-gray-900">
                                                Rp{{ number_format(is_numeric($item['price']) ? $item['price'] : 0, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @empty
                                        <div class="text-center py-8">
                                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            <p class="text-md text-gray-500">No items purchased for this event.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </details>
                        </div>

                        @if ($event['paymentStatus'] === 'confirmed')
                            <div class="mt-6 p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.99c.28 0 .53-.11.71-.29a1.003 1.003 0 000-1.42c-.18-.18-.43-.29-.71-.29H12m-6.08 0a1.003 1.003 0 000 1.42c.18.18.43.29.71.29h2.37z" />
                                    </svg>
                                    <p class="text-sm font-medium text-emerald-800">Entry QR Code</p>
                                </div>
                                <div class="flex items-center space-x-4">
                                    @if ($event['qr_code'])
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($event['qr_code']) }}"
                                            alt="QR Code for {{ $event['eventName'] }}"
                                            class="w-30 h-30 object-cover rounded-lg border-2 border-emerald-200 shadow-sm">
                                    @else
                                        <span class="text-sm text-emerald-700">QR code not generated yet.</span>
                                    @endif
                                    <div class="flex-1">
                                        <p class="text-sm text-emerald-700 mb-2">Show this QR code at the event entrance for
                                            quick check-in.</p>
                                        <p class="text-xs text-emerald-600">Event ID: {{ $event['eventId'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($event['paymentStatus'] === 'pending')
                            <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-amber-800">QR code will be available once payment is confirmed.
                                    </p>
                                </div>
                            </div>
                        @elseif ($event['paymentStatus'] === 'rejected')
                            <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-red-800">Payment was rejected. Please contact support for
                                        assistance.</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('event.show', ['id' => $event['eventId']]) }}"
                                class="inline-flex items-center bg-primary hover:bg-primary-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md transition-all duration-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transform hover:scale-105">
                                View Ticket Details
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                @if (!$error)
                    <div class="text-center py-16">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1V7a2 2 0 00-2-2H5zM5 14a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h3a1 1 0 011 1v1a1 1 0 01-1 1h-3a2 2 0 01-2-2V5zM11 14a2 2 0 012-2h3a1 1 0 011 1v1a1 1 0 01-1 1h-3a2 2 0 01-2-2v-1z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Tickets Yet</h3>
                        <p class="text-gray-600 mb-6">You don't have any confirmed tickets at the moment.</p>
                        <a href="{{ route('index') }}"
                            class="inline-flex items-center bg-primary hover:bg-primary-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-md transition-all duration-300">
                            Browse Events
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
    @include('components.footer')
@endsection
