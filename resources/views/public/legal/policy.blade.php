@extends('layouts.public.app')

@section('title', 'Política de Troca e Devolução')

@section('content')
    <x-legal.document
        eyebrow="Atendimento"
        title="Política de troca e devolução"
        description="Queremos que você compre com tranquilidade. Abaixo explicamos como solicitar uma troca, devolução ou atendimento em caso de problema com o pedido."
    >
        <h2>Prazo para desistência</h2>

        <p>
            Em compras realizadas pela internet, você pode solicitar a desistência da compra em até 7 dias corridos após o
            recebimento do produto. Para iniciar o atendimento, fale com a nossa equipe pelos canais informados nesta página.
        </p>

        <h2>Trocas e produtos com problema</h2>

        <p>
            Você pode solicitar atendimento quando receber um item diferente do pedido, com defeito de fabricação ou avariado
            durante o transporte. Informe o número do pedido, descreva o ocorrido e, se possível, envie fotos do produto e
            da embalagem.
        </p>

        <h2>Condições do produto</h2>

        <ul>
            <li>O item deve ser enviado com etiquetas e acessórios originais, quando aplicável.</li>
            <li>Produtos com sinais de uso, lavagem, odor ou alteração não poderão ser avaliados como troca por tamanho ou desistência.</li>
            <li>Em caso de defeito ou divergência no pedido, a análise e as orientações de envio serão informadas pela equipe.</li>
        </ul>

        <h2>Como solicitar</h2>

        <ol>
            <li>
                Entre em contato pelo WhatsApp
                <a
                    href="https://wa.me/5511931494708"
                    target="_blank"
                    class="font-semibold text-[#bd5564] hover:underline"
                >
                    (11) 93149-4708
                </a>.
            </li>

            <li>Informe seu nome, número do pedido e o motivo do contato.</li>
            <li>Aguarde as instruções de postagem ou de resolução enviadas pela equipe.</li>
        </ol>

        <h2>Reembolso</h2>

        <p>
            Quando houver devolução aprovada, o reembolso será tratado pelo mesmo meio de pagamento, respeitando os prazos
            operacionais da instituição financeira ou intermediador de pagamento.
        </p>

        <h2>Dúvidas</h2>

        <p>
            Se precisar de ajuda, envie uma mensagem pelo WhatsApp ou pelo e-mail
            <a
                href="mailto:elinealvesdelima5@gmail.com"
                class="font-semibold text-[#bd5564] hover:underline"
            >
                elinealvesdelima5@gmail.com
            </a>.
        </p>
    </x-legal.document>
@endsection
