<!DOCTYPE html>
<html lang="pt-BR">

@include('layouts.payments.partials.head')

<body class="min-h-screen bg-[#f8f3f1] font-sans text-[#2d2928]">
    <header class="border-b border-[#eaded9] bg-white px-5 py-4 text-center">
        <a
            href="{{ route('home') }}"
            class="font-['Cormorant_Garamond'] text-2xl font-semibold tracking-[0.08em]"
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
