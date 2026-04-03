@extends('layouts.payment')

@section('title', 'Pagamento com Cartão')

@section('content')
<div class="max-w-md mx-auto mt-12 p-6 bg-white shadow-xl rounded-xl">
    <h1 class="text-2xl font-bold mb-6 text-center">
        Pagamento com Cartão
    </h1>

    <div id="cardPaymentBrick_container"></div>
</div>
@endsection

@push('scripts')
<script src="https://sdk.mercadopago.com/js/v2"></script>

<script>
document.addEventListener("DOMContentLoaded", async function () {

    const mp = new MercadoPago("{{ env('MP_PUBLIC_KEY') }}", {
        locale: 'pt-BR'
    });

    const bricksBuilder = mp.bricks();

    const settings = {
        initialization: {
            amount: {{ number_format($order->total, 2, '.', '') }}
        },

        callbacks: {

            onReady: () => {
                console.log("✅ Brick carregado");
            },

            onSubmit: (formData) => {
                return new Promise(async (resolve, reject) => {
            
                    try {
                        const res = await fetch("{{ route('payment.card.process', $order->id) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                token: formData.token,
                                payment_method_id: formData.payment_method_id,
                                issuer_id: formData.issuer_id,
                                installments: formData.installments,
                                cpf: formData.payer.identification.number,
                                email: formData.payer.email
                            })
                        });
            
                        const result = await res.json();
            
                        if(result.status === 'paid'){
                            window.location.href = "/payment-success/{{ $order->id }}";
                        } else if(result.status === 'pending'){
                            window.location.href = "/payment-pending/{{ $order->id }}";
                        } else {
                            window.location.href = "/payment-error/{{ $order->id }}";
                        }
            
                        resolve();
            
                    } catch (e) {
                        console.error(e);
                        reject();
                    }
            
                });
            },

            onError: (error) => {
                console.error("❌ Erro Brick:", error);
            }
        }
    };

    await mp.bricks().create(
        "cardPayment",
        "cardPaymentBrick_container",
        settings
    );

});
</script>
@endpush