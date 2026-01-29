<!DOCTYPE html>
<html lang="vi" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Green Vibes Admin')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.6/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/alert.css') }}?v={{ time() }}">
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    @stack('styles')
    <link rel="icon" href="{{ asset('images/giangmai.jpg') }}" type="image/jpeg">
</head>

<body class="d-flex flex-column min-vh-100">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && systemDark)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
        (function() {
            const savedLayout = localStorage.getItem('layoutPos') || 'left';
            const body = document.body;

            if (savedLayout === 'right') {
                body.classList.add('sidebar-right');
            } else if (savedLayout === 'top') {
                body.classList.add('layout-top');
            } else if (savedLayout === 'bottom') {
                body.classList.add('layout-bottom');
            }
        })();
    </script>
    @include('layouts.admin.header')

    @include('layouts.admin.sidebar')
    <main class="main-content">
        @include('layouts.admin.alert')
        @yield('content')

    </main>
    @include('layouts.admin.footer')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/admin/js/main.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/admin/js/alert.js') }}?v={{ time() }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>