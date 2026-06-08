<!DOCTYPE html>
<html lang="pt-BR">

@include('layouts.public.partials.head')

<body class="bg-gray-50">

    @include('layouts.public.partials.header')

    <main class="min-h-screen">

        @include('components.ui.alerts')

        @yield('content')

    </main>

    @include('layouts.public.partials.footer')

    @include('layouts.public.partials.scripts')

</body>
</html>
