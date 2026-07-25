document.addEventListener("DOMContentLoaded",()=>{const o=document.getElementById("boletoStatus"),c=window.BOLETO_ORDER_ID,n=window.BOLETO_STATUS_URL,s=window.BOLETO_SUCCESS_URL,r=window.BOLETO_ERROR_URL;if(!c||!n||!s||!r){console.error("Dados necessários para consultar o status do boleto não encontrados.",{orderId:c,statusUrl:n,successUrl:s,errorUrl:r});return}let l=!1,t=null;const d=async()=>{if(!l){l=!0;try{const e=await fetch(n,{method:"GET",headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest"},cache:"no-store"});if(!e.ok)throw new Error(`Erro HTTP ${e.status}`);const i=await e.json();console.log("Status atual do boleto:",i);const a=i.status;if(a==="paid"){console.log("Boleto pago. Redirecionando para página de sucesso."),t&&(clearInterval(t),t=null),o&&(o.innerHTML=`
                        <p class="text-green-600 font-semibold">
                            Pagamento confirmado!
                        </p>

                        <p class="text-sm text-gray-500 mt-2">
                            Redirecionando...
                        </p>
                    `),window.location.href=s;return}if(a==="cancelled"||a==="expired"||a==="failed"){console.log("Boleto não concluído. Redirecionando para página de erro."),t&&(clearInterval(t),t=null),window.location.href=r;return}o&&(o.innerHTML=`
                    <p class="text-gray-600">
                        Aguardando confirmação do pagamento...
                    </p>

                    <p class="text-sm text-gray-400 mt-2">
                        O status será atualizado automaticamente.
                    </p>
                `)}catch(e){console.error("Erro ao consultar status do boleto:",e)}finally{l=!1}}};d(),t=setInterval(d,5e3),window.addEventListener("beforeunload",()=>{t&&(clearInterval(t),t=null)})});
