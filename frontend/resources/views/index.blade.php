@extends('components.layout')

@section('title')
    Collect Campus Experiences
@endsection

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
            <!-- Left Sidebar - Event Filters -->
            <div class="w-full lg:w-1/4 px-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 sticky top-0">
                    <h3 class="text-blue-900 text-sm font-semibold mb-5 pb-3 border-b-2 border-red-100">FILTER EVENTS</h3>
                    <nav class="flex flex-col gap-2">
                        <!-- Faculty Filter -->
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition active:text-red-600 active:font-medium"
                            href="#" data-faculty="">All Events</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"
                            data-faculty="FK"><i class="fas fa-laptop-code mr-2"></i>FK</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"
                            data-faculty="FKG"><i class="fas fa-flask mr-2"></i>FKG</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"
                            data-faculty="FP"><i class="fas fa-book mr-2"></i>FP</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"
                            data-faculty="FTRC"><i class="fas fa-graduation-cap mr-2"></i>FTRC</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"
                            data-faculty="FHIK"><i class="fas fa-chalkboard mr-2"></i>FHIK</a>
                        <a class="faculty-link text-gray-500 text-sm py-2 hover:text-red-600 transition" href="#"
                            data-faculty="FHBD"><i class="fas fa-building mr-2"></i>FHBD</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="w-full lg:w-1/2 px-4 mb-6">
                <!-- Search Bar with Date Range -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <form action="{{ url('/') }}" method="GET" class="flex items-center flex-wrap gap-2">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="keyword" placeholder="Search events..."
                                value="{{ request('keyword') }}"
                                class="w-full border border-gray-200 rounded px-4 py-2 focus:border-red-600 focus:ring focus:ring-red-100 transition">
                        </div>
                        <div class="w-45">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="w-full border border-gray-200 rounded px-4 py-2 focus:border-red-600 focus:ring focus:ring-red-100 transition">
                        </div>
                        <div class="w-45">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="w-full border border-gray-200 rounded px-4 py-2 focus:border-red-600 focus:ring focus:ring-red-100 transition">
                        </div>
                        <div class="">
                            <button type="submit"
                                class="w-10 h-10 bg-primary text-white rounded px-2 py-2 hover:bg-red-700 transition flex items-center justify-center">
                                <i class="fas fa-search"></i>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="events-container">
                        <!-- Events will be rendered here by JavaScript -->
                    </div>

                    <!-- Frontend Pagination -->
                    <div class="flex justify-center mt-8" id="pagination-controls">
                        <nav class="flex items-center gap-1">
                            <!-- Pagination buttons will be rendered here -->
                        </nav>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ url('/events') }}"
                            class="text-red-600 font-medium hover:text-blue-900 hover:underline transition">
                            See more events <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Schedule -->
            <div class="w-full lg:w-1/4 px-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 sticky top-0">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-blue-900 text-sm font-semibold">EVENT SCHEDULE</h3>
                        <div class="flex gap-2">
                            <button class="calendar-prev text-gray-500 hover:text-red-600 transition"><i
                                    class="fas fa-chevron-left"></i></button>
                            <button class="calendar-next text-gray-500 hover:text-red-600 transition"><i
                                    class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div class="mt-6">
                        <!-- Current Date -->
                        <div class="text-gray-600 text-xs mb-2">
                            {{ \Carbon\Carbon::now()->format('l, F d, Y') }} ({{ \Carbon\Carbon::now()->format('h:i A') }}
                            WIB)
                        </div>
                        <!-- Sample Schedule Items -->
                        @forelse ($events as $event)
                            <div class="py-3 border-b border-gray-100 last:border-b-0">
                                <div class="text-red-600 font-semibold text-sm">
                                    {{ \Carbon\Carbon::parse($event['start_time'])->format('M d, Y') }}
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No events scheduled today.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')
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

        // Frontend Pagination and Filtering
        const eventsContainer = document.querySelector('#events-container');
        const paginationControls = document.querySelector('#pagination-controls');
        const itemsPerPage = 6;
        let currentPage = 1;
        let currentFaculty = ''; // Empty for all faculties

        // Use fetched events data from backend
        const allEvents = @json($events);
        let filteredEvents = [...allEvents]; // Initial copy of all events

        function filterEvents(faculty) {
            currentFaculty = faculty;
            if (!faculty) {
                filteredEvents = [...allEvents]; // Show all events
            } else {
                filteredEvents = allEvents.filter(event =>
                    event.faculty && event.faculty.some(f => f.code === faculty)
                );
            }
            currentPage = 1; // Reset to first page on filter change
            renderEvents(currentPage);
            renderPagination();
        }

        function renderEvents(page) {
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const paginatedEvents = filteredEvents.slice(startIndex, endIndex);

            eventsContainer.innerHTML = '';

            if (paginatedEvents.length === 0) {
                eventsContainer.innerHTML = '<p class="text-gray-500">No events found.</p>';
                return;
            }

            paginatedEvents.forEach(event => {
                const facultyCode = event.faculty[0]?.code || 'N/A';
                const eventCard = `
                    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <img src="${event.poster_url || 'https://via.placeholder.com/300x200'}" class="w-full h-44 object-cover" alt="${event.name}">
                        <div class="p-4">
                            <h5 class="text-gray-900 text-base font-semibold mb-2">${event.name || event.full_name}</h5>
                            <div class="text-red-600 text-sm mb-3 flex items-center">
                                <i class="far fa-calendar-alt mr-2"></i>
                                ${new Date(event.start_time).toLocaleString('en-US', {
                                    month: 'short',
                                    day: 'numeric',
                                    year: 'numeric',
                                    hour: 'numeric',
                                    minute: 'numeric',
                                    hour12: true
                                })}
                            </div>
                            <div class="text-gray-500 text-sm mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt mr-2"></i> ${event.location || 'N/A'}
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <a href="/event/${event._id}"
                                    class="bg-red-50 text-primary text-sm font-medium px-4 py-1 rounded-full hover:bg-primary hover:text-white transition">
                                    Details
                                </a>
                            </div>
                            <div class="text-gray-600 text-xs mt-2">
                                Max Participants: ${event.max_participant || 'N/A'}
                            </div>
                        </div>
                    </div>
                `;
                eventsContainer.innerHTML += eventCard;
            });
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
            const nav = paginationControls.querySelector('nav');
            nav.innerHTML = '';

            // Previous Button
            const prevButton = `
                <a href="#" class="px-3 py-1 rounded border ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-600 hover:bg-gray-50'}" 
                   ${currentPage === 1 ? '' : `onclick="changePage(${currentPage - 1})"`}>
                    <i class="fas fa-chevron-left"></i>
                </a>
            `;
            nav.innerHTML += prevButton;

            // Page Numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            startPage = Math.max(1, endPage - 4);

            if (startPage > 1) {
                nav.innerHTML += `
                    <a href="#" class="px-3 py-1 rounded border bg-white text-gray-600 hover:bg-gray-50" onclick="changePage(1)">
                        1
                    </a>
                `;
                if (startPage > 2) {
                    nav.innerHTML += '<span class="px-2">...</span>';
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                nav.innerHTML += `
                    <a href="#" class="px-3 py-1 rounded border ${currentPage === i ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 hover:bg-gray-50'}" 
                       onclick="changePage(${i})">
                        ${i}
                    </a>
                `;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    nav.innerHTML += '<span class="px-2">...</span>';
                }
                nav.innerHTML += `
                    <a href="#" class="px-3 py-1 rounded border bg-white text-gray-600 hover:bg-gray-50" onclick="changePage(${totalPages})">
                        ${totalPages}
                    </a>
                `;
            }

            // Next Button
            const nextButton = `
                <a href="#" class="px-3 py-1 rounded border ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-600 hover:bg-gray-50'}" 
                   ${currentPage === totalPages ? '' : `onclick="changePage(${currentPage + 1})"`}>
                    <i class="fas fa-chevron-right"></i>
                </a>
            `;
            nav.innerHTML += nextButton;
        }

        function changePage(page) {
            currentPage = page;
            renderEvents(currentPage);
            renderPagination();
            eventsContainer.scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Initialize faculty filters
        facultyLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                facultyLinks.forEach(l => l.classList.remove('active:text-red-600', 'active:font-medium'));
                link.classList.add('active:text-red-600', 'active:font-medium');
                const faculty = link.getAttribute('data-faculty');
                filterEvents(faculty);
            });
        });

        // Initial render
        if (allEvents.length > 0) {
            renderEvents(currentPage);
            renderPagination();
        } else {
            eventsContainer.innerHTML = '<p class="text-gray-500">No events found.</p>';
        }
    </script>
@endpush
