@php
    $alerts = [];

    if (session()->has('success')) {
        $alerts[] = [
            'type' => 'success',
            'message' => session('success')
        ];
    }

    if (session()->has('error')) {
        $alerts[] = [
            'type' => 'error',
            'message' => session('error')
        ];
    }

    if (session()->has('warning')) {
        $alerts[] = [
            'type' => 'warning',
            'message' => session('warning')
        ];
    }

    $validationErrors = ($errors?->any())
        ? $errors->all()
        : [];
@endphp

@if(count($alerts) || count($validationErrors))
<script>
document.addEventListener('DOMContentLoaded', async function () {

    const alerts = @json($alerts);
    const errors = @json($validationErrors);

    for (const alert of alerts) {

        // SUCCESS (toast leve)
        if (alert.type === 'success') {
            await Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: alert.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }

        // WARNING (toast mais chamativo)
        else if (alert.type === 'warning') {
            await Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: alert.message,
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#fff8e1',
                iconColor: '#f59e0b'
            });
        }

        // ERROR (modal forte)
        else if (alert.type === 'error') {
            await Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: alert.message,
                confirmButtonColor: '#dc2626'
            });
        }
    }

    // ERROS DE VALIDAÇÃO (sempre modal)
    if (errors.length) {
        Swal.fire({
            icon: 'error',
            title: 'Erro de validação',
            html: errors.join('<br>'),
            confirmButtonColor: '#dc2626'
        });
    }

});
</script>
@endif
