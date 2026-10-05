<!DOCTYPE html>
<html lang="pt-BR">

@include('layouts.payments.partials.head')

<body class="store-shell min-h-screen font-sans">
    <header class="store-header border-b px-5 py-4 text-center">
        <a
            href="{{ route('home') }}"
            class="store-title text-2xl font-semibold tracking-[0.04em]"
        >
            MALU STORE
        </a>

        <p class="mt-1 text-[10px] font-bold uppercase tracking-[0.18em] text-[#c96f82]">
            Pagamento seguro
        </p>
    </header>

    <main class="flex min-h-[calc(100vh-88px)] items-center justify-center px-4 py-10">
        @yield('content')
    </main>

    @include('layouts.payments.partials.scripts')

    @stack('payment-scripts')
</body>

</html>
