document.addEventListener("DOMContentLoaded", async () => {

    const mp = new MercadoPago(
        window.MP_PUBLIC_KEY,
        {
            locale:'pt-BR'
        }
    );

    const settings = {

        initialization:{
            amount:Number(window.ORDER_TOTAL)
        },

        callbacks:{
            onReady:()=>{
                console.log("Brick carregado");
            },

            onSubmit:(formData)=>{

                console.log("========== FORMDATA ==========");
                console.log(formData);

                console.log("Token:", formData.token);
                console.log("Payment Method:", formData.payment_method_id);
                console.log("Issuer:", formData.issuer_id);
                console.log("Installments:", formData.installments);
                console.log("Email:", formData.payer.email);
                console.log("CPF:", formData.payer.identification.number);

                const payload = {
                    token: formData.token,
                    payment_method_id: formData.payment_method_id,
                    issuer_id: formData.issuer_id,
                    installments: formData.installments,
                    cpf: formData.payer.identification.number,
                    email: formData.payer.email
                };

                console.log("Payload enviado:", payload);

                return fetch(window.PAYMENT_URL, {
                    method:"POST",
                    headers:{
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": window.CSRF_TOKEN
                    },
                    body:JSON.stringify({

                        token:
                            formData.token,

                        payment_method_id:
                            formData.payment_method_id,

                        issuer_id:
                            formData.issuer_id,

                        installments:
                            formData.installments,

                        cpf:
                            formData.payer
                            .identification
                            .number,

                        email:
                            formData.payer.email
                    })
                })

                .then(response =>
                    response.json()
                )
                .then(result=>{
                    console.log("Resposta MP:", result);

                    if(result.status === "paid") {
                        window.location.href =
                        `/payment-success/${window.ORDER_ID}`;
                    }

                    else if(result.status === "pending") {
                        window.location.href =
                        `/payment-pending/${window.ORDER_ID}`;
                    }

                    else if(result.success === false) {
                        alert(
                            result.error ||
                            "Pagamento recusado"
                        );
                    }

                    else {
                        window.location.href =
                        `/payment-error/${window.ORDER_ID}`;
                    }
                })

                .catch(error=>{
                    console.error(
                        "Erro pagamento:",
                        error
                    );

                    alert("Erro ao processar pagamento");
                });
            },
            onError:(error)=>{
                console.error("Brick Error:", error);
            }
        }
    };

    await mp.bricks().create(
        "cardPayment",
        "cardPaymentBrick_container",
        settings
    );
});
