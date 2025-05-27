@extends('components.layout')

@section('title')
    Event Discovery Platform
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    @include('components.navbar')
    <!-- Hero Carousel -->
    <div class="relative h-96 overflow-hidden mb-8">
        <div class="w-full h-full">
            <!-- Carousel Wrapper -->
            <div class="carousel-wrapper flex h-full transition-transform duration-700 ease-in-out">
                <div class="carousel-slide flex-shrink-0 w-full h-full">
                    <img src="https://images.t2u.io/upload/a/0-1100-AWSS3f95c2b8c-55ac-4625-bbfe-d7c9fd52e6ec-owvD_M.PNG"
                        class="w-full h-full object-cover" alt="Concert Event">
                </div>
                <div class="carousel-slide flex-shrink-0 w-full h-full">
                    <img src="https://images.t2u.io/upload/a/0-1003-AWSS3fed91958-a395-4613-8857-29f09323efa5-hYg4_M.jpg"
                        class="w-full h-full object-cover" alt="Conference Event">
                </div>
            </div>

            <!-- Carousel Controls -->
            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2">
                <button class="carousel-indicator w-3 h-3 rounded-full bg-white focus:outline-none" data-index="0"></button>
                <button class="carousel-indicator w-3 h-3 rounded-full bg-white/50 focus:outline-none"
                    data-index="1"></button>
            </div>

            <!-- Navigation Arrows -->
            <button
                class="carousel-prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/30 text-white p-2 w-10 rounded-full hover:bg-white/50 focus:outline-none">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button
                class="carousel-next absolute right-4 top-1/2 -translate-y-1/2 bg-white/30 text-white p-2 w-10 rounded-full hover:bg-white/50 focus:outline-none">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="container mx-auto px-4">
        <div class="flex flex-wrap -mx-4">
            <!-- Left Sidebar - Faculty Filter -->
            <div class="w-full lg:w-1/4 px-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 sticky top-0">
                    <h3 class="text-blue-900 text-sm font-semibold mb-5 pb-3 border-b-2 border-red-100">FILTER EVENTS</h3>
                    <nav class="flex flex-col gap-2">
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition active:text-red-600 active:font-medium"
                            href="#"><i class="fas fa-university mr-2"></i>All Events</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"><i
                                class="fas fa-laptop-code mr-2"></i>FTRC</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"><i
                                class="fas fa-flask mr-2"></i>FKG</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"><i
                                class="fas fa-business-time mr-2"></i>FSRD</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"><i
                                class="fas fa-ellipsis-h mr-2"></i>Other</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="w-full lg:w-1/2 px-4 mb-6">
                <!-- Search Bar -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form action="{{ url('/') }}" method="GET" class="flex flex-wrap gap-2">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="keyword" placeholder="Search events..."
                                value="{{ request('keyword') }}"
                                class="w-full border border-gray-200 rounded px-4 py-2 focus:border-red-600 focus:ring focus:ring-red-100 transition">
                        </div>
                        <div class="w-45">
                            <input type="date" name="date" value="{{ request('date') }}"
                                class="w-full border border-gray-200 rounded px-4 py-2 focus:border-red-600 focus:ring focus:ring-red-100 transition">
                        </div>
                        <div class="w-32">
                            <button type="submit"
                                class="w-full bg-primary text-white rounded px-4 py-2 hover:bg-red-700 transition">
                                <i class="fas fa-search mr-2"></i> Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Events Section -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2
                        class="text-blue-900 font-semibold mb-6 pb-3 relative after:absolute after:bottom-0 after:left-0 after:w-12 after:h-1 after:bg-primary">
                        UPCOMING EVENTS
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($events as $index => $eventArray)
                            <div
                                class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <img src="{{ $eventArray['poster_url'] }}" class="w-full h-44 object-cover"
                                    alt="{{ $eventArray['name'] }}">
                                <div class="p-4">
                                    <h5 class="text-gray-900 text-base font-semibold mb-2">{{ $eventArray['name'] }}</h5>
                                    <div class="text-red-600 text-sm mb-3 flex items-center">
                                        <i class="far fa-calendar-alt mr-2"></i>
                                        {{ \Carbon\Carbon::parse($eventArray['date_time'])->format('M d, Y • h:i A') }}
                                    </div>
                                    <div class="text-gray-500 text-sm mb-4 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i> {{ $eventArray['location'] }}
                                    </div>
                                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                        <span
                                            class="text-red-600 font-semibold">${{ $eventArray['registration_fee']['$numberDecimal'] }}</span>
                                        <a href="{{ url('/event/' . $eventArray['_id']) }}"
                                            class="bg-red-50 text-primary text-sm font-medium px-4 py-1 rounded-full hover:bg-primary hover:text-white transition">
                                            Details
                                        </a>
                                    </div>
                                    <div class="text-gray-600 text-xs mt-2">
                                        Event #{{ $index + 1 }} | Speaker: {{ $eventArray['speaker'] }}<br />Max
                                        Participants:
                                        {{ $eventArray['max_participants'] }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No events found.</p>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($totalPages > 1)
                        <div class="flex justify-center mt-8">
                            <nav class="flex gap-2">
                                @for ($i = 1; $i <= $totalPages; $i++)
                                    <a href="{{ url('/') }}?page={{ $i }}&location={{ $locationFilter }}&keyword={{ $keyword }}&date={{ $date }}&faculty={{ $faculty }}"
                                        class="px-4 py-2 rounded {{ $currentPage == $i ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }} hover:bg-primary hover:text-white transition">
                                        {{ $i }}
                                    </a>
                                @endfor
                            </nav>
                        </div>
                    @endif

                    <div class="text-center mt-8">
                        <a href="{{ url('/events') }}"
                            class="text-red-600 font-medium hover:text-blue-900 hover:underline transition">
                            See more events <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Calendar -->
            <div class="w-full lg:w-1/4 px-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 sticky top-0">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-blue-900 text-sm font-semibold">May 2025</h3>
                        <div class="flex gap-2">
                            <button class="calendar-prev text-gray-500 hover:text-red-600 transition"><i
                                    class="fas fa-chevron-left"></i></button>
                            <button class="calendar-next text-gray-500 hover:text-red-600 transition"><i
                                    class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="py-3 border-b border-gray-100 last:border-b-0">
                            <div class="text-red-600 font-semibold text-sm">May 5</div>
                            <div class="text-gray-900 text-sm">Tech Workshop Series</div>
                        </div>
                        <div class="py-3 border-b border-gray-100 last:border-b-0">
                            <div class="text-red-600 font-semibold text-sm">May 11-13</div>
                            <div class="text-gray-900 text-sm">International Conference</div>
                        </div>
                        <div class="py-3 border-b border-gray-100 last:border-b-0">
                            <div class="text-red-600 font-semibold text-sm">May 20</div>
                            <div class="text-gray-900 text-sm">Career Fair</div>
                        </div>
                        <div class="py-3 border-b border-gray-100 last:border-b-0">
                            <div class="text-red-600 font-semibold text-sm">May 25</div>
                            <div class="text-gray-900 text-sm">Cultural Festival</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <footer class="bg-white mt-auto border-t-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} Tixin. All rights reserved.
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/"
                        class="text-sm text-gray-600 hover:bg-primary/20 hover:text-red-600 px-3 py-2 rounded-md transition duration-200">
                        Home
                    </a>
                    <a href="#"
                        class="text-sm text-gray-600 hover:bg-primary/20 hover:text-red-600 px-3 py-2 rounded-md transition duration-200">
                        Terms
                    </a>
                    <a href="#"
                        class="text-sm text-gray-600 hover:bg-primary/20 hover:text-red-600 px-3 py-2 rounded-md transition duration-200">
                        Privacy Policy
                    </a>
                </div>
                @if (auth()->check() && session('role'))
                    <div class="text-sm text-gray-600">
                        Logged in as <span class="font-semibold capitalize">{{ session('role') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </footer>
@endsection

@push('script')
    <script>
        // Carousel functionality
        const carouselWrapper = document.querySelector('.carousel-wrapper');
        const carouselSlides = document.querySelectorAll('.carousel-slide');
        const carouselIndicators = document.querySelectorAll('.carousel-indicator');
        const carouselPrev = document.querySelector('.carousel-prev');
        const carouselNext = document.querySelector('.carousel-next');
        let currentIndex = 0;

        function showSlide(index) {
            if (index >= carouselSlides.length) index = 0;
            if (index < 0) index = carouselSlides.length - 1;
            carouselWrapper.style.transform = `translateX(-${index * 100}%)`;

            carouselIndicators.forEach((indicator, i) => {
                indicator.classList.toggle('bg-white', i === index);
                indicator.classList.toggle('bg-white/50', i !== index);
            });

            currentIndex = index;
        }

        carouselIndicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => showSlide(index));
        });

        if (carouselPrev) {
            carouselPrev.addEventListener('click', () => showSlide(currentIndex - 1));
        }

        if (carouselNext) {
            carouselNext.addEventListener('click', () => showSlide(currentIndex + 1));
        }

        // Auto-advance carousel
        setInterval(() => {
            showSlide(currentIndex + 1);
        }, 5000);

        // Faculty filter tabs
        const facultyLinks = document.querySelectorAll('.faculty-link');
        facultyLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            facultyLinks.forEach(l => l.classList.remove('active:text-red-600',
                'active:font-medium'));
            this.classList.add('active:text-red-600', 'active:font-medium');
        });
        });
        });
    </script>
@endpush
