@extends('layouts.public.app')

@section('title', 'Política de Privacidade')

@section('content')
    <x-legal.document eyebrow="Privacidade" title="Política de privacidade" description="Explicamos de forma simples quais dados são usados para atender pedidos, manter sua conta e melhorar a experiência na Malu Store.">
        <h2>Dados que podemos utilizar</h2>
        <p>Podemos utilizar dados cadastrais e de contato, como nome, e-mail, telefone, CPF e endereço de entrega, além de informações necessárias para identificar pedidos, pagamentos, fretes e atendimento.</p>

        <h2>Finalidades</h2>
        <ul>
            <li>criar e administrar sua conta;</li>
            <li>processar pedidos, pagamentos, entregas e trocas;</li>
            <li>prestar atendimento e responder solicitações;</li>
            <li>cumprir obrigações legais e prevenir fraudes;</li>
            <li>melhorar a operação e a segurança da loja.</li>
        </ul>

        <h2>Compartilhamento</h2>
        <p>Dados necessários para a operação podem ser compartilhados com parceiros de pagamento, transporte e infraestrutura, apenas para as finalidades relacionadas ao pedido e ao funcionamento do serviço.</p>

        <h2>Segurança e retenção</h2>
        <p>Adotamos controles de acesso e medidas técnicas compatíveis com a operação. Os dados são mantidos pelo tempo necessário para as finalidades informadas e para o cumprimento de obrigações legais ou regulatórias.</p>

        <h2>Seus direitos</h2>
        <p>Você pode solicitar informações sobre o tratamento dos seus dados, acesso, correção, atualização e outras providências previstas na legislação aplicável. Para isso, entre em contato pelos canais abaixo.</p>

        <h2>Contato</h2>
        <p>Para assuntos de privacidade, escreva para <a class="font-semibold text-[#bd5564] hover:underline" href="mailto:elinealvesdelima5@gmail.com">elinealvesdelima5@gmail.com</a>.</p>
    </x-legal.document>
@endsection
