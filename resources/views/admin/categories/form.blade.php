@csrf

@if($errors->any())
    <div class="mb-4 rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] p-4 text-sm text-[#b44259]">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div>
    <label class="mb-2 block text-sm font-semibold text-[#443d3b]">Nome</label>
    <input type="text" name="name"
           value="{{ old('name', $category->name ?? '') }}"
           class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
</div>

<div>
    <label class="mb-2 block text-sm font-semibold text-[#443d3b]">Slug</label>
    <input type="text" name="slug"
           value="{{ old('slug', $category->slug ?? '') }}"
           class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
</div>
