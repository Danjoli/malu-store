document.addEventListener("DOMContentLoaded", async () => {

    const mp = new MercadoPago(window.MP_PUBLIC_KEY, {
        locale: 'pt-BR'
    });

    const bricksBuilder = mp.bricks();

    const settings = {
        initialization: {
            amount: Number(window.ORDER_TOTAL)
        },

        callbacks: {

            onReady: () => {
                console.log("Brick carregado");
            },

            onSubmit: (formData) => {

                return fetch(window.PAYMENT_URL, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": window.CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        token: formData.token,
                        payment_method_id: formData.payment_method_id,
                        issuer_id: formData.issuer_id,
                        installments: formData.installments,
                        cpf: formData.payer.identification.number,
                        email: formData.payer.email
                    })
                })
                .then(res => res.json())
                .then(result => {

                    if (result.status === 'paid') {
                        window.location.href = `/payment-success/${window.ORDER_ID}`;
                    }
                    else if (result.status === 'pending') {
                        window.location.href = `/payment-pending/${window.ORDER_ID}`;
                    }
                    else if (result.success === false) {
                        alert(result.error?.message || "Erro no pagamento");
                    }
                    else {
                        window.location.href = `/payment-error/${window.ORDER_ID}`;
                    }
                });
            },

            onError: (error) => {
                console.error("Erro:", error);
            }
        }
    };

    await mp.bricks().create(
        "cardPayment",
        "cardPaymentBrick_container",
        settings
    );
});
