@php
    $alerts = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('error')   ? ['type' => 'error',   'message' => session('error')]   : null,
        session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
    ])->filter();

    $validationErrors = $errors->any() ? $errors->all() : [];
@endphp

@if($alerts->isNotEmpty() || count($validationErrors))
<script>
document.addEventListener('DOMContentLoaded', function () {

    @foreach ($alerts as $alert)
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: @json($alert['type']),
            title: @json($alert['message']),
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endforeach

    @if(count($validationErrors))
        Swal.fire({
            icon: 'error',
            title: 'Erro de validação',
            html: @json(implode('<br>', $validationErrors)),
            confirmButtonColor: '#3085d6'
        });
    @endif

});
</script>
@endif
