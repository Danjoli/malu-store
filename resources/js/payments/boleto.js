setInterval(async () => {
        try {
            const response = await fetch("{{ route('payment.status', $order->id) }}");
            const data = await response.json();

            if (data.status === 'expired') {
                window.location.href = "{{ route('payment.error', ['order' => $order->id, 'reason' => 'expired']) }}";
            }

            if (data.status === 'paid') {
                window.location.href = "{{ route('payment.success', $order->id) }}";
            }

        } catch (e) {
            console.error('Erro ao verificar status');
        }
    }, 5000);
