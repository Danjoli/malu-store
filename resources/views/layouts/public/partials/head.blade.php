<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#9f4055">

    <script>
        (() => {
            const savedTheme = localStorage.getItem('malu-store-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const dark = savedTheme ? savedTheme === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', dark);
            document.documentElement.dataset.theme = dark ? 'dark' : 'light';

            window.storeTheme = {
                set(nextDark) {
                    document.documentElement.classList.toggle('dark', nextDark);
                    document.documentElement.dataset.theme = nextDark ? 'dark' : 'light';
                    localStorage.setItem('malu-store-theme', nextDark ? 'dark' : 'light');
                    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', nextDark ? '#171315' : '#9f4055');
                },
            };
        })();
    </script>

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

    <title>@yield('title') - Malu Store</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
