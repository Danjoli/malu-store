<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Mercado Pago SDK -->
    <script src="https://sdk.mercadopago.com/js/v2" defer></script>

    @stack('styles')
    
    <title>@yield('title')</title>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

    @yield('content')

<script>

function copiarPix(){

    let textarea = document.getElementById('pixCode');

    textarea.select();
    textarea.setSelectionRange(0,99999);

    document.execCommand("copy");

    alert("Código PIX copiado!");

}

</script>

@stack('scripts')

</body>
</html>
