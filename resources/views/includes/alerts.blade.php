@if(session('success'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: @json(session("success")),
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'error',
    title: @json(session("error")),
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true
});
</script>
@endif

@if(session('warning'))
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'warning',
    title: @json(session("warning")),
    showConfirmButton: false,
    timer: 4000,
    timerProgressBar: true
});
</script>
@endif

@if ($errors->any())
<script>
let messages = '';
@foreach ($errors->all() as $error)
    messages += @json($error) + '<br>';
@endforeach

Swal.fire({
    icon: 'error',
    title: 'Erro de validação',
    html: messages,
    confirmButtonColor: '#3085d6'
});
</script>
@endif