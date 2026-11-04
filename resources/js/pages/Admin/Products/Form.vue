<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    mode: { type: String, required: true },
    categories: { type: Array, required: true },
    sizes: { type: Array, required: true },
    product: { type: Object, default: null },
    urls: { type: Object, required: true },
});

const isEditing = computed(() => props.mode === 'edit');
const defaultSize = props.sizes.includes('M') ? 'M' : props.sizes[0];
const newVariant = () => ({ color: '', size: defaultSize, stock: 0 });

const form = useForm({
    name: props.product?.name ?? '',
    category_id: props.product?.category_id ?? props.categories[0]?.id ?? '',
    price: props.product?.price ?? '',
    description: props.product?.description ?? '',
    details: props.product?.details ?? '',
    composition: props.product?.composition ?? '',
    active: props.product?.active ?? true,
    images: [],
    variants: props.product?.variants?.length
        ? props.product.variants.map((variant) => ({ ...variant }))
        : [newVariant()],
});

const existingPreviews = (props.product?.images ?? []).map((image) => ({
    id: `existing-${image.id}`,
    url: image.url,
    name: props.product.name,
    temporary: false,
}));
const previews = ref(existingPreviews);
const selectedPreview = ref(previews.value[0] ?? null);
const commonColors = ['Amarelo', 'Azul Claro', 'Azul Marinho', 'Azul Royal', 'Bordô', 'Marrom', 'Natural', 'Preto', 'Rosa', 'Verde Menta', 'Vermelho'];

const addVariant = () => form.variants.push(newVariant());
const removeVariant = (index) => {
    if (form.variants.length > 1) {
        form.variants.splice(index, 1);
    }
};

const clearTemporaryPreviews = () => {
    previews.value
        .filter((preview) => preview.temporary)
        .forEach((preview) => URL.revokeObjectURL(preview.url));
};

const selectImages = (event) => {
    clearTemporaryPreviews();
    const files = Array.from(event.target.files ?? []);
    form.images = files;

    if (!files.length) {
        previews.value = existingPreviews;
        selectedPreview.value = previews.value[0] ?? null;
        return;
    }

    previews.value = files.map((file, index) => ({
        id: `new-${index}-${file.lastModified}`,
        url: URL.createObjectURL(file),
        name: file.name,
        temporary: true,
    }));
    selectedPreview.value = previews.value[0];
};

const errorFor = (field) => form.errors[field];

const submit = () => {
    form.transform((data) => isEditing.value ? { ...data, _method: 'put' } : data)
        .post(props.urls.submit, {
            forceFormData: true,
            preserveScroll: true,
        });
};

onBeforeUnmount(clearTemporaryPreviews);
</script>

<template>
    <Head :title="isEditing ? 'Editar produto' : 'Novo produto'" />

    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#b85d70]">Catálogo</p>
        <h1 class="mt-1 font-['Cormorant_Garamond'] text-4xl font-semibold text-[#2d2928]">
            {{ isEditing ? 'Editar produto' : 'Cadastrar produto' }}
        </h1>
        <p class="mt-2 text-sm text-[#746b68]">
            Revise as fotos em tamanho grande e organize cores, tamanhos e estoque em uma única tela.
        </p>
    </div>

    <form @submit.prevent="submit" class="grid items-start gap-8 xl:grid-cols-[minmax(0,1fr)_minmax(380px,520px)]">
        <div class="order-2 space-y-6 xl:order-1">
            <section class="space-y-5 rounded-2xl border border-[#eaded9] bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold">Informações do produto</h2>

                <div>
                    <label for="product-name" class="mb-1 block font-medium">Nome do produto</label>
                    <input id="product-name" v-model="form.name" type="text" class="w-full rounded-xl border border-[#ddc9c4] p-3 focus:border-[#cf7184] focus:outline-none" autocomplete="off">
                    <p v-if="errorFor('name')" class="mt-1 text-sm text-red-700">{{ errorFor('name') }}</p>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="product-category" class="mb-1 block font-medium">Categoria</label>
                        <select id="product-category" v-model="form.category_id" class="w-full rounded-xl border border-[#ddc9c4] p-3">
                            <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                        </select>
                        <p v-if="errorFor('category_id')" class="mt-1 text-sm text-red-700">{{ errorFor('category_id') }}</p>
                    </div>

                    <div>
                        <label for="product-price" class="mb-1 block font-medium">Preço</label>
                        <div class="flex overflow-hidden rounded-xl border border-[#ddc9c4] focus-within:border-[#cf7184]">
                            <span class="bg-[#f8f3f1] px-3 py-3 font-bold text-[#746b68]">R$</span>
                            <input id="product-price" v-model="form.price" type="number" min="0" step="0.01" class="min-w-0 flex-1 p-3 focus:outline-none">
                        </div>
                        <p v-if="errorFor('price')" class="mt-1 text-sm text-red-700">{{ errorFor('price') }}</p>
                    </div>
                </div>

                <div>
                    <label for="product-description" class="mb-1 block font-medium">Descrição</label>
                    <p class="mb-2 text-sm text-[#746b68]">Use a foto ao lado para descrever tecido, modelagem, cor e acabamento.</p>
                    <textarea id="product-description" v-model="form.description" rows="7" class="w-full rounded-xl border border-[#ddc9c4] p-3 focus:border-[#cf7184] focus:outline-none"></textarea>
                    <p v-if="errorFor('description')" class="mt-1 text-sm text-red-700">{{ errorFor('description') }}</p>
                </div>

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="product-details" class="mb-1 block font-medium">Detalhes da peça</label>
                        <p class="mb-2 text-sm text-[#746b68]">Modelagem, caimento, fechamento, bolsos e cuidados.</p>
                        <textarea id="product-details" v-model="form.details" rows="5" class="w-full rounded-xl border border-[#ddc9c4] p-3 focus:border-[#cf7184] focus:outline-none"></textarea>
                        <p v-if="errorFor('details')" class="mt-1 text-sm text-red-700">{{ errorFor('details') }}</p>
                    </div>
                    <div>
                        <label for="product-composition" class="mb-1 block font-medium">Composição</label>
                        <p class="mb-2 text-sm text-[#746b68]">Informe os materiais e percentuais disponíveis na etiqueta.</p>
                        <textarea id="product-composition" v-model="form.composition" rows="5" placeholder="Ex.: 70% viscose, 27% linho e 3% elastano." class="w-full rounded-xl border border-[#ddc9c4] p-3 focus:border-[#cf7184] focus:outline-none"></textarea>
                        <p v-if="errorFor('composition')" class="mt-1 text-sm text-red-700">{{ errorFor('composition') }}</p>
                    </div>
                </div>

                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-[#eaded9] bg-[#fdf9f7] p-4">
                    <span>
                        <strong class="block">Produto ativo</strong>
                        <small class="text-[#746b68]">Quando desligado, o produto fica oculto na loja.</small>
                    </span>
                    <input v-model="form.active" type="checkbox" class="h-6 w-6 accent-[#cf7184]">
                </label>
            </section>

            <section class="rounded-2xl border border-[#eaded9] bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold">Cores, tamanhos e estoque</h2>
                        <p class="text-sm text-[#746b68]">Cada combinação precisa ser única.</p>
                    </div>
                    <button type="button" @click="addVariant" class="rounded-xl bg-green-700 px-4 py-2 font-bold text-white hover:bg-green-800">+ Adicionar variação</button>
                </div>

                <p v-if="errorFor('variants')" class="mt-3 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ errorFor('variants') }}</p>

                <div class="mt-4 space-y-3">
                    <div v-for="(variant, index) in form.variants" :key="variant.id ?? `new-${index}`" class="grid gap-3 rounded-xl border border-[#eaded9] bg-[#fdf9f7] p-4 sm:grid-cols-[1fr_100px_120px_auto]">
                        <div>
                            <label :for="`variant-color-${index}`" class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#746b68]">Cor</label>
                            <input :id="`variant-color-${index}`" v-model="variant.color" type="text" list="common-colors" placeholder="Ex.: Azul Royal" class="w-full rounded-lg border border-[#ddc9c4] p-2.5">
                            <p v-if="errorFor(`variants.${index}.color`)" class="mt-1 text-xs text-red-700">{{ errorFor(`variants.${index}.color`) }}</p>
                        </div>
                        <div>
                            <label :for="`variant-size-${index}`" class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#746b68]">Tamanho</label>
                            <select :id="`variant-size-${index}`" v-model="variant.size" class="w-full rounded-lg border border-[#ddc9c4] p-2.5">
                                <option v-for="size in sizes" :key="size" :value="size">{{ size }}</option>
                            </select>
                        </div>
                        <div>
                            <label :for="`variant-stock-${index}`" class="mb-1 block text-xs font-bold uppercase tracking-wide text-[#746b68]">Estoque</label>
                            <input :id="`variant-stock-${index}`" v-model.number="variant.stock" type="number" min="0" class="w-full rounded-lg border border-[#ddc9c4] p-2.5">
                        </div>
                        <button type="button" @click="removeVariant(index)" :disabled="form.variants.length === 1" class="self-end rounded-lg border border-red-200 px-3 py-2.5 text-sm font-bold text-red-700 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-40">Remover</button>
                    </div>
                </div>

                <datalist id="common-colors">
                    <option v-for="color in commonColors" :key="color" :value="color" />
                </datalist>
            </section>

            <div class="flex items-center justify-between gap-4">
                <a :href="urls.index" class="font-bold text-[#746b68] hover:text-[#2d2928]">Cancelar</a>
                <button type="submit" :disabled="form.processing" class="rounded-xl bg-[#2d2928] px-6 py-3 font-bold text-white transition hover:bg-[#443d3b] disabled:cursor-wait disabled:opacity-60">
                    {{ form.processing ? 'Salvando…' : (isEditing ? 'Atualizar produto' : 'Cadastrar produto') }}
                </button>
            </div>

            <div v-if="form.progress" class="overflow-hidden rounded-full bg-[#eaded9]" aria-label="Progresso do envio">
                <div class="h-2 bg-[#cf7184] transition-all" :style="{ width: `${form.progress.percentage}%` }"></div>
            </div>
        </div>

        <aside class="order-1 xl:sticky xl:top-8 xl:order-2" aria-labelledby="product-image-heading">
            <div class="overflow-hidden rounded-2xl border border-[#ddc9c4] bg-white shadow-[0_18px_50px_rgba(76,50,47,0.12)]">
                <div class="border-b border-[#eaded9] px-5 py-4">
                    <h2 id="product-image-heading" class="text-lg font-bold">Visualização da roupa</h2>
                    <p class="mt-1 text-sm text-[#746b68]">Selecione uma miniatura para ampliar. Novas fotos aparecem antes de salvar.</p>
                </div>

                <div id="productImageStage" class="relative flex aspect-[4/5] max-h-[70vh] min-h-[420px] items-center justify-center bg-[#f3ebe7]">
                    <img v-if="selectedPreview" id="productImageStageImage" :src="selectedPreview.url" :alt="`Visualização de ${form.name || 'produto'}`" class="h-full w-full object-contain">
                    <div v-else class="px-8 text-center text-[#746b68]">
                        <span class="block text-5xl" aria-hidden="true">▧</span>
                        <p class="mt-4 font-semibold text-[#443d3b]">A imagem grande aparecerá aqui</p>
                        <p class="mt-2 text-sm">Escolha uma ou mais fotos da roupa para começar.</p>
                    </div>
                </div>

                <div class="space-y-4 border-t border-[#eaded9] p-5">
                    <div>
                        <label for="product-images" class="mb-2 block text-sm font-bold">{{ isEditing ? 'Selecionar novas imagens' : 'Selecionar imagens' }}</label>
                        <input id="product-images" type="file" multiple accept="image/jpeg,image/png,image/webp" @change="selectImages" class="w-full rounded-lg border border-[#ddc9c4] p-2 text-sm">
                        <p v-if="isEditing" class="mt-2 text-xs text-[#857b78]">Ao salvar com novas imagens, a galeria atual será substituída.</p>
                        <p v-if="errorFor('images')" class="mt-1 text-sm text-red-700">{{ errorFor('images') }}</p>
                    </div>

                    <div id="imagePreview" class="grid grid-cols-4 gap-2" aria-label="Miniaturas do produto">
                        <button v-for="preview in previews" :key="preview.id" type="button" @click="selectedPreview = preview" :class="['overflow-hidden rounded-lg border-2 bg-[#f3ebe7] transition', selectedPreview?.id === preview.id ? 'border-[#cf7184]' : 'border-transparent hover:border-[#e59aaa]']">
                            <img :src="preview.url" alt="" class="aspect-square w-full object-cover">
                        </button>
                    </div>
                </div>
            </div>
        </aside>
    </form>
</template>
