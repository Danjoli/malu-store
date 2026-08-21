document.addEventListener('DOMContentLoaded', () => {
    const calculator = document.querySelector('[data-product-shipping]');

    if (!calculator) return;

    const input = calculator.querySelector('[data-shipping-cep]');
    const button = calculator.querySelector('[data-shipping-submit]');
    const feedback = calculator.querySelector('[data-shipping-feedback]');
    const results = calculator.querySelector('[data-shipping-results]');
    const endpoint = calculator.dataset.endpoint;
    const productId = calculator.dataset.productId;
    const money = (value) => Number(value).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
    });

    input.addEventListener('input', () => {
        const digits = input.value.replace(/\D/g, '').slice(0, 8);
        input.value = digits.replace(/(\d{5})(\d)/, '$1-$2');
    });

    button.addEventListener('click', async () => {
        const cep = input.value.replace(/\D/g, '');

        if (cep.length !== 8) {
            feedback.textContent = 'Informe um CEP com 8 dígitos.';
            feedback.className = 'mt-3 text-xs font-medium text-red-600';
            results.classList.add('hidden');
            input.focus();
            return;
        }

        button.disabled = true;
        button.textContent = 'Calculando...';
        feedback.textContent = 'Consultando opções de entrega...';
        feedback.className = 'mt-3 text-xs text-stone-500';
        results.classList.add('hidden');

        try {
            const { data } = await window.axios.post(endpoint, {
                cep,
                product_id: productId,
            });
            const options = Array.isArray(data) ? data.filter((option) => Number(option.price) > 0) : [];

            if (!options.length) {
                throw new Error('Nenhuma opção de entrega está disponível para este CEP.');
            }

            results.innerHTML = options.map((option) => `
                <li class="flex items-center justify-between gap-3 border-t border-[#eee6e4] py-3 first:border-t-0 first:pt-0">
                    <span>
                        <strong class="block text-xs text-stone-800">${option.name || 'Entrega'}</strong>
                        <small class="text-[11px] text-stone-500">Prazo estimado: ${option.delivery_time ?? '-'} dias úteis</small>
                    </span>
                    <strong class="whitespace-nowrap text-xs text-stone-800">${money(option.price)}</strong>
                </li>
            `).join('');
            feedback.textContent = 'Valores estimados para uma unidade deste produto.';
            feedback.className = 'mt-3 text-[11px] text-stone-500';
            results.classList.remove('hidden');
        } catch (error) {
            feedback.textContent = error.response?.data?.mensagem
                || error.response?.data?.errors?.cep?.[0]
                || error.message
                || 'Não foi possível calcular o frete agora.';
            feedback.className = 'mt-3 text-xs font-medium text-red-600';
        } finally {
            button.disabled = false;
            button.textContent = 'Calcular';
        }
    });
});
