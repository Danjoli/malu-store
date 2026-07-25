function p(){const o=document.getElementById("boletoStatus"),i=window.BOLETO_ORDER_ID,a=window.BOLETO_STATUS_URL,s=window.BOLETO_SUCCESS_URL,r=window.BOLETO_ERROR_URL;if(!i||!a||!s||!r){console.error("Dados necessários para consultar o status do boleto não encontrados.",{orderId:i,statusUrl:a,successUrl:s,errorUrl:r});return}let c=!1,e=null;const l=()=>{e&&(clearInterval(e),e=null)},d=async()=>{if(!c){c=!0;try{const t=await fetch(a,{method:"GET",headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest"},cache:"no-store"});if(!t.ok)throw new Error(`Erro HTTP ${t.status}`);const u=await t.json();console.log("Status atual do boleto:",u);const n=u.status;if(n==="paid"){console.log("Boleto pago. Redirecionando para página de sucesso."),l(),o&&(o.innerHTML=`
                        <p class="text-green-600 font-semibold">
                            Pagamento confirmado!
                        </p>

                        <p class="text-sm text-gray-500 mt-2">
                            Redirecionando...
                        </p>
                    `),window.location.href=s;return}if(n==="cancelled"||n==="expired"||n==="failed"){console.log("Boleto não concluído. Redirecionando para página de erro."),l(),window.location.href=r;return}o&&(o.innerHTML=`
                    <p class="text-gray-600">
                        Aguardando confirmação do pagamento...
                    </p>

                    <p class="text-sm text-gray-400 mt-2">
                        O status será atualizado automaticamente.
                    </p>
                `)}catch(t){console.error("Erro ao consultar status do boleto:",t)}finally{c=!1}}};d(),e=setInterval(d,5e3),window.addEventListener("beforeunload",l)}document.addEventListener("DOMContentLoaded",()=>{p()});
