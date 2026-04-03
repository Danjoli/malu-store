@extends('layouts.app')

@section('title', 'Política de Troca')

@section('content')

<div class="container mx-auto px-6 py-10 max-w-4xl bg-white rounded shadow mt-20">

    <h1 class="text-3xl font-bold mb-6">Política de Troca</h1>

    <p class="mb-4">
        Na Malu Store, nossa prioridade é a sua satisfação. Caso haja algum problema com o seu pedido,
        você pode solicitar a troca ou devolução dentro do prazo de 7 dias após o recebimento do produto.
    </p>

    <h2 class="text-xl font-semibold mb-2">Produtos Elegíveis para Troca</h2>
    <ul class="list-disc list-inside mb-4">
        <li>Produtos com defeito de fabricação.</li>
        <li>Produtos que não correspondam ao pedido realizado.</li>
        <li>Produtos que chegaram danificados durante o transporte.</li>
    </ul>

    <h2 class="text-xl font-semibold mb-2">Como Solicitar a Troca</h2>
    <ol class="list-decimal list-inside mb-4">
        <li>Entre em contato pelo WhatsApp:
            <a href="https://wa.me/5511931494708" target="_blank" class="text-green-600 hover:underline font-semibold">
                (11) 93149-4708
            </a>.
        </li>
        <li>Informe o número do pedido e o motivo da troca.</li>
        <li>Envie o produto seguindo as instruções que receberá por mensagem.</li>
    </ol>

    <h2 class="text-xl font-semibold mb-2">Observações Importantes</h2>
    <p>
        Produtos com sinais de uso, violação da embalagem original ou fora do prazo não serão aceitos para troca.
        As despesas de envio podem ser de responsabilidade do cliente, salvo em casos de defeito ou erro da loja.
    </p>

    <p class="mt-6 text-sm text-gray-600">
        Para dúvidas adicionais, entre em contato com nossa equipe via WhatsApp. Estamos aqui para garantir que sua experiência de compra seja excelente!
    </p>

</div>

@endsection
