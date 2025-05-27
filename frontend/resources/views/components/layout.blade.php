<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collex - @yield('title')</title>
    <link rel="icon" href="{{ asset('x.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('style')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body>

    <main>
        @yield('content')
    </main>

    @stack('script')

</body>

</html>
