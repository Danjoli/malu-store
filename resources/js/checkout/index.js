document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('checkout-form');
    if (!form) return;

    const money = (value) => Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    const fields = ['label', 'recipient_name', 'phone', 'cep', 'street', 'number', 'complement', 'neighborhood', 'city', 'state'];
    const addresses = Array.isArray(window.CHECKOUT_ADDRESSES) ? window.CHECKOUT_ADDRESSES : [];
    const addressId = document.getElementById('address_id');
    const addressEditor = document.getElementById('address-editor');
    const editorTitle = document.getElementById('address-editor-title');
    const newAddressActions = document.getElementById('new-address-actions');
    const newAddressFeedback = document.getElementById('new-address-feedback');
    const shippingCost = document.getElementById('shipping_cost');
    const carrier = document.getElementById('carrier');
    const service = document.getElementById('service');
    const shippingStatus = document.getElementById('shipping-status');
    const shippingError = document.getElementById('shipping-error');
    const shippingPreview = document.getElementById('shipping-cep-preview');
    const fretesContainer = document.getElementById('fretes-container');
    const fretesList = document.getElementById('lista-fretes');
    const input = (name) => form.querySelector(`[name="${name}"]`);
    const cleanCep = () => (input('cep')?.value || '').replace(/\D/g, '');

    const invalidateShipping = (message = 'Calcule o frete para o endereço selecionado.') => {
        shippingCost.value = '';
        carrier.value = '';
        service.value = '';
        fretesList.innerHTML = '';
        fretesContainer.classList.add('hidden');
        shippingError.classList.add('hidden');
        shippingStatus.textContent = message;
        shippingStatus.classList.remove('hidden');
        document.getElementById('valor-frete').textContent = money(0);
        document.getElementById('valor-total').textContent = money(window.SUBTOTAL ?? 0);
    };

    const syncPreviews = () => {
        shippingPreview.value = input('cep')?.value || '';
        document.getElementById('order-phone-preview').value = input('phone')?.value || '';
    };
    const fillAddress = (address) => {
        fields.forEach((name) => { if (input(name)) input(name).value = address?.[name] ?? ''; });
        addressId.value = address?.id ?? '';
        syncPreviews();
        invalidateShipping();
    };

    const bindAddressChoice = (radio) => {
        radio.addEventListener('change', () => {
            fillAddress(addresses.find((address) => String(address.id) === radio.value));
            addressEditor.classList.add('hidden');
        });
    };
    document.querySelectorAll('[name="address_choice"]').forEach(bindAddressChoice);

    const appendAddressCard = (address) => {
        const container = document.getElementById('address-options');
        const label = document.createElement('label');
        label.className = 'address-card relative cursor-pointer rounded-md border bg-white p-4 transition';
        label.dataset.addressId = address.id;
        const radio = document.createElement('input');
        radio.type = 'radio'; radio.name = 'address_choice'; radio.value = address.id;
        radio.className = 'peer sr-only'; radio.checked = true;
        const marker = document.createElement('span');
        marker.className = 'absolute right-4 top-4 h-4 w-4 rounded-full border border-stone-300 peer-checked:border-[5px] peer-checked:border-[#d66f7c]';
        const title = document.createElement('span');
        title.className = 'block pr-7 text-sm font-bold text-stone-800'; title.textContent = address.label || 'Endereço';
        const details = document.createElement('span');
        details.className = 'mt-2 block text-xs leading-5 text-stone-500';
        details.textContent = `${address.street}, ${address.number}\n${address.neighborhood} · ${address.city}/${address.state}\nCEP ${address.cep}`;
        details.style.whiteSpace = 'pre-line';
        label.append(radio, marker, title, details);
        container.appendChild(label);
        container.classList.remove('hidden');
        bindAddressChoice(radio);
        return radio;
    };
    document.getElementById('btn-new-address')?.addEventListener('click', () => {
        document.querySelectorAll('[name="address_choice"]').forEach((radio) => { radio.checked = false; });
        fillAddress(null);
        editorTitle.textContent = 'Adicionar novo endereço';
        addressEditor.classList.remove('hidden');
        newAddressActions.classList.remove('hidden');
        newAddressFeedback.classList.add('hidden');
        input('recipient_name')?.focus();
    });
    document.getElementById('btn-edit-address')?.addEventListener('click', () => {
        editorTitle.textContent = 'Editar endereço selecionado';
        addressEditor.classList.remove('hidden');
        newAddressActions.classList.add('hidden');
        input('recipient_name')?.focus();
    });
    document.getElementById('btn-close-address-editor')?.addEventListener('click', () => {
        addressEditor.classList.add('hidden');
        newAddressActions.classList.add('hidden');
    });

    document.getElementById('btn-save-new-address')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const requiredFields = ['recipient_name', 'phone', 'cep', 'street', 'number', 'neighborhood', 'city', 'state'];
        const invalid = requiredFields.map(input).find((field) => !field?.checkValidity());
        if (invalid) {
            invalid.reportValidity();
            return;
        }

        const payload = Object.fromEntries(fields.map((name) => [name, input(name)?.value || '']));
        button.disabled = true;
        button.textContent = 'Salvando...';
        newAddressFeedback.classList.add('hidden');
        try {
            const { data } = await axios.post('/addresses', payload, {
                headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, Accept: 'application/json' },
            });
            const saved = data.address;
            addresses.push(saved);
            document.querySelectorAll('[name="address_choice"]').forEach((radio) => { radio.checked = false; });
            appendAddressCard(saved);
            addressId.value = saved.id;
            fillAddress(saved);
            addressEditor.classList.add('hidden');
            newAddressActions.classList.add('hidden');
            if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2500 });
        } catch (error) {
            const errors = error.response?.data?.errors;
            newAddressFeedback.textContent = errors ? Object.values(errors).flat().join(' ') : 'Não foi possível salvar o endereço. Tente novamente.';
            newAddressFeedback.className = 'mt-3 text-sm font-semibold text-red-600';
        } finally {
            button.disabled = false;
            button.textContent = 'Salvar e usar este endereço';
        }
    });

    input('cep')?.addEventListener('input', () => {
        syncPreviews();
        invalidateShipping('O CEP mudou. Calcule novamente para ver opções válidas.');
    });
    input('phone')?.addEventListener('input', syncPreviews);

    document.getElementById('cpf')?.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '').slice(0, 11);
        value = value.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        this.value = value;
    });

    document.getElementById('btn-calcular-frete')?.addEventListener('click', async (event) => {
        const button = event.currentTarget;
        const cep = cleanCep();
        if (cep.length !== 8) {
            shippingError.textContent = 'Informe um CEP com 8 dígitos antes de calcular.';
            shippingError.classList.remove('hidden');
            input('cep')?.focus();
            return;
        }
        invalidateShipping('Consultando opções dos Correios...');
        button.textContent = 'Calculando...';
        button.disabled = true;
        try {
            const { data } = await axios.post('/frete/calcular', { cep }, { headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN } });
            const options = Array.isArray(data) ? data.filter((option) => Number(option.price) > 0) : [];
            if (!options.length) throw new Error('Nenhuma opção de entrega disponível.');
            options.forEach((option) => {
                const label = document.createElement('label');
                label.className = 'frete-card flex cursor-pointer items-center gap-4 rounded-md border border-[#eadfdd] bg-white p-4 transition hover:border-[#d66f7c]';
                const radio = document.createElement('input');
                radio.type = 'radio'; radio.name = 'frete_opcao'; radio.value = option.price;
                radio.dataset.carrier = option.name || 'Correios'; radio.dataset.service = option.id;
                radio.className = 'h-4 w-4 accent-[#d66f7c]';
                const details = document.createElement('span'); details.className = 'flex-1';
                const name = document.createElement('strong'); name.className = 'block text-sm text-stone-800'; name.textContent = option.name || 'Correios';
                const time = document.createElement('small'); time.className = 'mt-1 block text-xs text-stone-500'; time.textContent = `Prazo estimado: ${option.delivery_time} dias`;
                const price = document.createElement('strong'); price.className = 'text-sm text-stone-800'; price.textContent = money(option.price);
                details.append(name, time); label.append(radio, details, price); fretesList.appendChild(label);
            });
            shippingStatus.classList.add('hidden');
            fretesContainer.classList.remove('hidden');
        } catch (error) {
            shippingStatus.textContent = error.response?.data?.mensagem || error.message || 'Não foi possível calcular o frete agora.';
            shippingError.classList.remove('hidden');
        } finally {
            button.textContent = 'Calcular frete';
            button.disabled = false;
        }
    });

    const toggleCardFields = () => {
        const isCard = form.querySelector('[name="payment_method"]:checked')?.value === 'card';
        document.getElementById('card-fields').classList.toggle('hidden', !isCard);
        ['card_number', 'holder_name', 'expiration_month', 'expiration_year', 'ccv'].forEach((name) => { input(name).required = isCard; });
    };
    document.addEventListener('change', (event) => {
        if (event.target.name === 'frete_opcao') {
            const value = Number(event.target.value);
            shippingCost.value = value.toFixed(2);
            carrier.value = event.target.dataset.carrier;
            service.value = event.target.dataset.service;
            shippingError.classList.add('hidden');
            document.getElementById('valor-frete').textContent = money(value);
            document.getElementById('valor-total').textContent = money(Number(window.SUBTOTAL ?? 0) + value);
        }
        if (event.target.name === 'payment_method') toggleCardFields();
    });
    form.addEventListener('submit', (event) => {
        if (!(Number(shippingCost.value) > 0) || !carrier.value || !service.value) {
            event.preventDefault();
            shippingError.textContent = 'Selecione uma opção de entrega válida antes de finalizar.';
            shippingError.classList.remove('hidden');
            document.getElementById('checkout-shipping-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    syncPreviews();
    invalidateShipping();
    toggleCardFields();
});
