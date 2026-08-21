<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#c95f75">

    <link
        rel="icon"
        type="image/svg+xml"
        href="{{ asset('favicon.svg') }}?v=2"
    >

    <title>@yield('title') | Admin</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])
</head>
