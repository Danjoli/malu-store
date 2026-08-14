@extends('layouts.public.app')

@section('title', 'Termos de Uso')

@section('content')
    <x-legal.document eyebrow="Informações legais" title="Termos de uso" description="Estes termos explicam as regras básicas para navegar, criar uma conta e comprar na Malu Store.">
        <h2>Uso da loja</h2>
        <p>Ao utilizar este site, você declara que fornecerá informações verdadeiras e manterá seus dados cadastrais atualizados. A conta é pessoal e o usuário é responsável por proteger sua senha.</p>

        <h2>Produtos, preços e estoque</h2>
        <p>As informações de produto, preço e disponibilidade são apresentadas no site e podem ser atualizadas. A confirmação do pedido depende da aprovação do pagamento e da disponibilidade do item no momento do processamento.</p>

        <h2>Pedidos e pagamentos</h2>
        <p>O pagamento é processado pelos meios disponibilizados no checkout. Caso a cobrança não seja aprovada, o pedido poderá permanecer pendente ou ser cancelado. Não armazenamos os dados completos do cartão no painel da loja.</p>

        <h2>Entrega</h2>
        <p>Os prazos e valores de frete são apresentados de acordo com o endereço informado. O cliente deve revisar os dados de entrega antes de concluir o pedido. Eventuais atualizações de rastreio ficam disponíveis conforme a transportadora informar o status.</p>

        <h2>Propriedade intelectual</h2>
        <p>Textos, fotos, marca, identidade visual e demais conteúdos da Malu Store não podem ser copiados ou utilizados sem autorização prévia.</p>

        <h2>Alterações</h2>
        <p>Estes termos podem ser atualizados para refletir mudanças na operação da loja ou na legislação aplicável. A versão publicada nesta página é a válida no momento da consulta.</p>
    </x-legal.document>
@endsection
