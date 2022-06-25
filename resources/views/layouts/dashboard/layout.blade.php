<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    <link rel="icon" href="{{ URL::asset('images/laravel-logo.png') }}" type="image/x-icon"/>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

    @include('layouts.dashboard.header')

    <div class="loader">
        <div class="container-fluid mb-5">
            <div class="row">
                @include('layouts.dashboard.sidebar')

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    @yield('content')
                </main>
            </div>
        </div>

        <footer class="footer mt-auto py-3 bg-light">
            <div class="container">
                <div class="offset-md-2 text-center">
                    <span class="text-muted">
                        @component('components.laravel-logo')
                            @slot('width', '40px')
                        @endcomponent
                        @component('components.application-version')@endcomponent
                    </span>
                </div>
            </div>
        </footer>

    </div>

    @component('components.modal')@endcomponent

    <div id="loader"></div>

    <!-- Scripts -->
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>
</html>
