<!DOCTYPE html>
<html lang="pt-br">

@include('layouts.admin.partials.head')

<body class="bg-gray-100 font-sans">

<div class="flex h-screen">

    @include('layouts.admin.partials.sidebar')

    <main class="flex-1 p-10 overflow-y-auto h-screen">

        @include('includes.alerts')

        @yield('content')

    </main>

</div>

@include('layouts.admin.partials.scripts')

</body>
</html>
