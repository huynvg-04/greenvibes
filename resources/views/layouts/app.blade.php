<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-logged-in" content="{{ Auth::check() ? '1' : '0' }}">
    <title>@yield('title', 'Green Vibes')</title>

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;300;400;600&display=swap">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap">
    </noscript>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.6/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href=" {{ asset('css/home.css') }}">
    <link rel="icon" href="{{ asset('images/giangmai.jpg') }}" type="image/jpeg">
    @stack('styles')
</head>

<body>
    <div class="page-loader"></div>
    @include('layouts.user.header')
    
    @include('layouts.user.chat')
    
    <main role="main">
        <div class="container px-0">
            @yield('content')
        </div>
    </main>

   @include('layouts.user.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.AppConfig = { routes: { suggestions: "{{ route('products.suggestions') }}" } };
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>