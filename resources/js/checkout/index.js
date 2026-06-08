// FORMATAR R$
const formatar = (v) => {
    return v.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
};

// CPF máscara
const cpfInput = document.getElementById('cpf');
cpfInput.addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'');
    v = v.replace(/(\d{3})(\d)/,'$1.$2');
    v = v.replace(/(\d{3})(\d)/,'$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    this.value = v;
});

const totalBase = {{ $subtotal }};

// CALCULAR FRETE
document.getElementById('btn-calcular-frete').addEventListener('click', async () => {

    const cep = document.querySelector('input[name="cep"]').value;

    if (!cep || cep.length < 8) {
        alert('Digite um CEP válido');
        return;
    }

    const btn = document.getElementById('btn-calcular-frete');
    btn.innerText = 'Calculando...';
    btn.disabled = true;

    const response = await fetch('/frete/calcular', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cep })
    });

    const data = await response.json();

    btn.innerText = 'Calcular Frete';
    btn.disabled = false;

    const container = document.getElementById('fretes-container');
    const lista = document.getElementById('lista-fretes');

    lista.innerHTML = '';

    data.forEach(frete => {

        if (!frete.price) return;

        const div = document.createElement('div');
        div.classList.add('border', 'p-3', 'rounded-lg', 'cursor-pointer');

        div.innerHTML = `
            <label class="flex justify-between items-center">
                <div>
                    <input type="radio" name="frete_opcao"
                        value="${frete.price}"
                        data-carrier="${frete.name}"
                        data-service="${frete.id}">
                    <strong>${frete.name}</strong><br>
                    <small>${frete.delivery_time} dias</small>
                </div>
                <span>${formatar(parseFloat(frete.price))}</span>
            </label>
        `;

        lista.appendChild(div);
    });

    container.classList.remove('hidden');
});

// ESCOLHER FRETE
document.addEventListener('change', function(e) {
    if (e.target.name === 'frete_opcao') {

        const valor = parseFloat(e.target.value);
        const carrier = e.target.dataset.carrier;
        const service = e.target.dataset.service;

        document.getElementById('shipping_cost').value = valor;
        document.getElementById('carrier').value = carrier;
        document.getElementById('service').value = service;

        document.getElementById('valor-frete').innerText = formatar(valor);

        const total = totalBase + valor;
        document.getElementById('valor-total').innerText = formatar(total);

        // destaque
        document.querySelectorAll('#lista-fretes > div').forEach(el => {
            el.classList.remove('border-green-500');
        });

        e.target.closest('div').classList.add('border-green-500');
    }
});

// VALIDAR SUBMIT
document.querySelector('form').addEventListener('submit', function(e) {
    if (!document.getElementById('shipping_cost').value) {
        e.preventDefault();
        alert('Selecione um frete antes de finalizar.');
    }
});
