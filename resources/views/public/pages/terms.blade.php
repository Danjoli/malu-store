@extends('layouts.app')

@section('title', 'Termos de Uso')

@section('content')

<div class="container mx-auto px-6 py-10 mt-20 max-w-4xl bg-white rounded shadow">

    <h1 class="text-3xl font-bold mb-6">Termos de Uso</h1>

    <p class="mb-4">
        Bem-vindo à Malu Store. Ao acessar e utilizar nosso site, você concorda com os seguintes termos e condições:
    </p>

    <h2 class="text-xl font-semibold mb-2">1. Uso do Site</h2>
    <p class="mb-4">
        Você concorda em utilizar a loja apenas para fins legais e de acordo com todas as leis aplicáveis.
        É proibido reproduzir, distribuir ou utilizar nossos conteúdos sem autorização.
    </p>

    <h2 class="text-xl font-semibold mb-2">2. Compras e Pagamentos</h2>
    <p class="mb-4">
        Todas as compras estão sujeitas à disponibilidade de estoque. Os preços podem ser alterados sem aviso prévio.
        O pagamento deve ser concluído através dos métodos fornecidos no site.
    </p>

    <h2 class="text-xl font-semibold mb-2">3. Entrega e Responsabilidade</h2>
    <p class="mb-4">
        A Malu Store não se responsabiliza por atrasos causados por transportadoras ou informações incorretas de endereço.
        É responsabilidade do cliente fornecer dados corretos.
    </p>

    <h2 class="text-xl font-semibold mb-2">4. Privacidade</h2>
    <p class="mb-4">
        Seus dados pessoais serão utilizados conforme nossa <a href="{{ route('privacy') }}" class="text-blue-600 hover:underline">Política de Privacidade</a>.
    </p>

    <h2 class="text-xl font-semibold mb-2">5. Alterações nos Termos</h2>
    <p>
        Reservamo-nos o direito de modificar estes termos a qualquer momento. Alterações serão publicadas nesta página.
    </p>

</div>

@endsection
