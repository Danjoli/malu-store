@csrf

@if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div>
    <label class="block mb-1 font-semibold">Nome</label>
    <input type="text" name="name"
           value="{{ old('name', $category->name ?? '') }}"
           class="w-full border p-2 rounded">
</div>

<div>
    <label class="block mb-1 font-semibold">Slug</label>
    <input type="text" name="slug"
           value="{{ old('slug', $category->slug ?? '') }}"
           class="w-full border p-2 rounded">
</div>

