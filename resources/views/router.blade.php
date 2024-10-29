@php use Carbon\Carbon; @endphp
        <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title')
    </title>
    {{--    @routes--}}
    <!-- Scripts -->
    {{--    <script> window.isAuthenticated = {!! (auth()->check())? 1 : 0!!}</script>--}}
    {{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">--}}

    <!-- Fonts -->
    {{--    <link rel="dns-prefetch" href="//fonts.gstatic.com">--}}
    {{--    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">--}}
    <link rel="icon" href="{{asset('/img/logo_Upatras.png')}}" type="image/x-icon">
    <!-- Styles -->

    {{--    <link rel="preload" href="{{ Vite::asset('resources/sass/app.scss') }}" as="style" onload="this.rel='stylesheet'">--}}
    {{--    <script src="{{Vite::asset('resources/js/main.js')}}" ></script>--}}
    @vite(['resources/js/main.js'])

    @yield('Styles')
</head>
<body>
<div id="app">
</div>
</body>
</html>
