<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#9f4055">

    @include('layouts.partials.theme-init')

    <link
        rel="icon"
        type="image/svg+xml"
        href="{{ asset('favicon.svg') }}?v=2"
    >
    <link
        rel="alternate icon"
        type="image/x-icon"
        href="{{ asset('favicon.ico') }}?v=3"
    >

    <title>@yield('title')</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    @stack('styles')
</head>
