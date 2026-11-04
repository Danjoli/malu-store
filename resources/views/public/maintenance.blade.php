<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Voltamos em breve | {{ $settings->store_name }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="store-shell min-h-screen font-sans antialiased">
    <main class="flex min-h-screen items-center justify-center px-5 py-12">
        <section class="store-card w-full max-w-2xl p-8 text-center sm:p-12">
            <div class="store-brand mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[var(--store-accent)] text-[var(--store-accent-contrast)]">MS</div>
            <p class="store-kicker mt-7">Pausa rápida</p>
            <h1 class="store-title mt-3 text-5xl sm:text-6xl">Estamos preparando novidades</h1>
            <p class="store-muted mx-auto mt-5 max-w-lg leading-7">A {{ $settings->store_name }} está temporariamente em manutenção para deixar sua experiência ainda melhor. Volte em breve.</p>
            @if ($settings->support_email)
                <a href="mailto:{{ $settings->support_email }}" class="store-button store-button-outline mt-7">Falar com atendimento</a>
            @endif
        </section>
    </main>
</body>
</html>
