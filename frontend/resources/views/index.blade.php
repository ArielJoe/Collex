@extends('components.layout')

@section('title')
    Tixin - Event Discovery Platform
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e63946;
            --primary-light: #f8f1f1;
            --secondary: #1d3557;
            --light: #f1faee;
            --dark: #0a0a0a;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
            font-size: 1.5rem;
        }

        .hero-carousel {
            height: 400px;
            overflow: hidden;
            border-radius: 0 0 10px 10px;
        }

        .hero-carousel img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }

        .carousel-indicators button {
            width: 10px;
            height: 10px;
        }

        .faculty-filter {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .faculty-filter h3 {
            color: var(--secondary);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.75rem;
        }

        .faculty-filter .nav-link {
            color: var(--gray);
            padding: 0.5rem 0;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .faculty-filter .nav-link:hover,
        .faculty-filter .nav-link.active {
            color: var(--primary);
            font-weight: 500;
        }

        .search-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .search-input {
            border: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(230, 57, 70, 0.15);
        }

        .search-btn {
            background: var(--primary);
            border: none;
            padding: 0.75rem 1.5rem;
            transition: all 0.2s;
        }

        .search-btn:hover {
            background: #d62839;
        }

        .date-filter {
            background: var(--light-gray);
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .date-filter:hover {
            background: #e9ecef;
        }

        .events-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .section-title {
            color: var(--secondary);
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
        }

        .event-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .event-img {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }

        .event-body {
            padding: 1.25rem;
        }

        .event-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .event-date {
            font-size: 0.8rem;
            color: var(--primary);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
        }

        .event-date i {
            margin-right: 0.5rem;
        }

        .event-location {
            font-size: 0.8rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .event-location i {
            margin-right: 0.5rem;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.75rem;
            border-top: 1px solid #f1f1f1;
        }

        .event-price {
            font-weight: 600;
            color: var(--primary);
        }

        .event-action {
            background: var(--primary-light);
            color: var(--primary);
            border: none;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .event-action:hover {
            background: var(--primary);
            color: white;
        }

        .calendar-widget {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--secondary);
        }

        .calendar-nav button {
            background: none;
            border: none;
            color: var(--gray);
            padding: 0.25rem 0.5rem;
        }

        .calendar-nav button:hover {
            color: var(--primary);
        }

        .calendar-day {
            padding: 0.5rem;
            text-align: center;
            font-size: 0.85rem;
        }

        .calendar-day.active {
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .calendar-events {
            margin-top: 1.5rem;
        }

        .calendar-event {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f1f1;
        }

        .calendar-event:last-child {
            border-bottom: none;
        }

        .event-day {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .event-name {
            font-size: 0.85rem;
            color: var(--dark);
        }

        .see-more {
            display: inline-block;
            margin-top: 2rem;
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .see-more:hover {
            color: var(--secondary);
            text-decoration: underline;
        }

        @media (max-width: 991.98px) {
            .hero-carousel {
                height: 300px;
            }
        }

        @media (max-width: 767.98px) {
            .hero-carousel {
                height: 250px;
            }

            .event-card {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush

@push('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Activate carousel
            $('.carousel').carousel({
                interval: 5000
            });

            // Faculty filter tabs
            $('.faculty-link').on('click', function(e) {
                e.preventDefault();
                $('.faculty-link').removeClass('active');
                $(this).addClass('active');
                // Here you would typically filter events via AJAX
            });

            // Calendar navigation
            $('.calendar-prev').on('click', function() {
                // Previous month logic
            });

            $('.calendar-next').on('click', function() {
                // Next month logic
            });
        });
    </script>
@endpush

@section('content')
    <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide hero-carousel mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.t2u.io/upload/a/0-1100-AWSS3f95c2b8c-55ac-4625-bbfe-d7c9fd52e6ec-owvD_M.PNG"
                    class="d-block w-100" alt="Concert Event">
                <div class="carousel-caption d-none d-md-block">
                    {{-- <h5>Summer Music Festival 2025</h5>
                    <p>Join us for the biggest music event of the year!</p>
                    <a href="#" class="btn btn-primary btn-sm">Get Tickets</a> --}}
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://images.t2u.io/upload/a/0-1100-AWSS3f95c2b8c-55ac-4625-bbfe-d7c9fd52e6ec-owvD_M.PNG"
                    class="d-block w-100" alt="Conference Event">
                <div class="carousel-caption d-none d-md-block">
                    {{-- <h5>Tech Conference 2025</h5>
                    <p>Learn from industry leaders and innovators</p>
                    <a href="#" class="btn btn-primary btn-sm">Register Now</a> --}}
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container">
        <div class="row">
            <!-- Left Sidebar - Faculty Filter -->
            <div class="col-lg-3 mb-4">
                <div class="faculty-filter sticky-top" style="top: 20px;">
                    <h3>FILTER EVENTS</h3>
                    <nav class="nav flex-column">
                        <a class="nav-link faculty-link active" href="#"><i class="fas fa-university me-2"></i>All
                            Events</a>
                        <a class="nav-link faculty-link" href="#"><i class="fas fa-laptop-code me-2"></i>FTRC</a>
                        <a class="nav-link faculty-link" href="#"><i class="fas fa-flask me-2"></i>FKG</a>
                        <a class="nav-link faculty-link" href="#"><i class="fas fa-business-time me-2"></i>FSRD</a>
                        <a class="nav-link faculty-link" href="#"><i class="fas fa-ellipsis-h me-2"></i>Other</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-6 mb-4">
                <!-- Search Bar -->
                <div class="search-container mb-4">
                    <form action="" method="GET" class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="keyword" placeholder="Search events..."
                                value="{{ request('keyword') }}" class="form-control search-input">
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <input type="date" class="form-control search-input" placeholder="Select date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 search-btn">
                                <i class="fas fa-search me-2"></i> Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Events Section -->
                <div class="events-section">
                    <h2 class="section-title">UPCOMING EVENTS</h2>
                    <div class="row">
                        @for ($i = 0; $i < 6; $i++)
                            <div class="col-md-6">
                                <div class="card event-card">
                                    <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80"
                                        class="event-img" alt="Event Image">
                                    <div class="event-body">
                                        <h5 class="event-title">Annual Tech Symposium 2025</h5>
                                        <div class="event-date">
                                            <i class="far fa-calendar-alt"></i> May 15, 2025 • 9:00 AM
                                        </div>
                                        <div class="event-location">
                                            <i class="fas fa-map-marker-alt"></i> University Auditorium
                                        </div>
                                        <div class="event-footer">
                                            <span class="event-price">FREE</span>
                                            <button class="btn btn-sm event-action">Details</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="text-center">
                        <a href="#" class="see-more">
                            See more events <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar - Calendar -->
            <div class="col-lg-3 mb-4">
                <div class="calendar-widget sticky-top" style="top: 20px;">
                    <div class="calendar-header">
                        <h3 class="calendar-title">May 2025</h3>
                        <div class="calendar-nav">
                            <button class="calendar-prev"><i class="fas fa-chevron-left"></i></button>
                            <button class="calendar-next"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>

                    <div class="calendar-events">
                        <div class="calendar-event">
                            <div class="event-day">May 5</div>
                            <div class="event-name">Tech Workshop Series</div>
                        </div>
                        <div class="calendar-event">
                            <div class="event-day">May 11-13</div>
                            <div class="event-name">International Conference</div>
                        </div>
                        <div class="calendar-event">
                            <div class="event-day">May 20</div>
                            <div class="event-name">Career Fair</div>
                        </div>
                        <div class="calendar-event">
                            <div class="event-day">May 25</div>
                            <div class="event-name">Cultural Festival</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
