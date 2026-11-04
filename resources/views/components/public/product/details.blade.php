@props(['product', 'reviews' => collect(), 'eligibleReviewItems' => collect()])

@php
    $defaultReviewItem = $eligibleReviewItems->first();
    $existingReview = $defaultReviewItem?->review;
@endphp

<section id="descricao" class="mt-8 border-t border-[#eee6e4] pt-5" data-product-tabs>
    <div class="flex gap-5 overflow-x-auto border-b border-[#eee6e4] text-[10px] font-bold uppercase tracking-wide text-stone-700" role="tablist" aria-label="Informações do produto">
        <button type="button" class="shrink-0 border-b-2 border-[#d66f7c] pb-3" role="tab" aria-selected="true" aria-controls="product-description" data-product-tab="description">Descrição</button>
        <button type="button" class="shrink-0 border-b-2 border-transparent pb-3" role="tab" aria-selected="false" aria-controls="product-details" data-product-tab="details">Detalhes</button>
        <button type="button" class="shrink-0 border-b-2 border-transparent pb-3" role="tab" aria-selected="false" aria-controls="product-composition" data-product-tab="composition">Composição</button>
        <button id="avaliacoes-tab" type="button" class="shrink-0 border-b-2 border-transparent pb-3" role="tab" aria-selected="false" aria-controls="avaliacoes" data-product-tab="reviews">Avaliações ({{ $product->reviews_count }})</button>
    </div>

    <div id="product-description" class="max-w-3xl py-5 text-xs leading-6 text-stone-600" role="tabpanel" data-product-panel="description">{!! nl2br(e($product->description)) !!}</div>

    <div id="product-details" class="hidden max-w-3xl py-5 text-xs leading-6 text-stone-600" role="tabpanel" data-product-panel="details">
        @if (filled($product->details))
            {!! nl2br(e($product->details)) !!}
        @else
            <p>Os detalhes específicos desta peça ainda não foram informados.</p>
        @endif
    </div>

    <div id="product-composition" class="hidden max-w-3xl py-5 text-xs leading-6 text-stone-600" role="tabpanel" data-product-panel="composition">
        @if (filled($product->composition))
            {!! nl2br(e($product->composition)) !!}
        @else
            <p>A composição desta peça ainda não foi informada. Consulte a etiqueta do produto ou fale com o atendimento.</p>
        @endif
    </div>

    <div id="avaliacoes" class="hidden py-6" role="tabpanel" data-product-panel="reviews">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(300px,420px)]">
            <div>
                <div class="mb-5 flex flex-wrap items-end gap-3">
                    <strong class="text-4xl text-stone-900">{{ $product->reviews_count ? number_format($product->reviews_avg_rating, 1, ',', '.') : '—' }}</strong>
                    <div>
                        <p class="text-base tracking-wider text-amber-500">
                            @if ($product->reviews_count)
                                {{ str_repeat('★', (int) round($product->reviews_avg_rating)) }}{{ str_repeat('☆', 5 - (int) round($product->reviews_avg_rating)) }}
                            @else
                                ☆☆☆☆☆
                            @endif
                        </p>
                        <p class="text-xs text-stone-500">Baseado em {{ $product->reviews_count }} {{ $product->reviews_count === 1 ? 'avaliação' : 'avaliações' }}</p>
                    </div>
                </div>

                @forelse ($reviews as $review)
                    <article class="border-t border-[#eee6e4] py-5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="text-sm tracking-wider text-amber-500">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                                @if ($review->title)<h3 class="mt-1 text-sm font-bold text-stone-800">{{ $review->title }}</h3>@endif
                            </div>
                            <time class="text-[11px] text-stone-400" datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('d/m/Y') }}</time>
                        </div>
                        <p class="mt-3 text-xs leading-6 text-stone-600">{{ $review->comment }}</p>
                        <p class="mt-3 text-[11px] font-semibold text-stone-500">
                            {{ Str::before($review->user->name, ' ') }} ·
                            @if ($review->is_demo)
                                <span class="text-amber-700">Avaliação demonstrativa</span>
                            @else
                                <span class="text-emerald-700">Compra verificada</span>
                            @endif
                        </p>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-[#d9cecb] p-6 text-center">
                        <p class="text-sm font-bold text-stone-700">Este produto ainda não recebeu avaliações.</p>
                        <p class="mt-2 text-xs text-stone-500">As primeiras opiniões aparecerão aqui após compras entregues.</p>
                    </div>
                @endforelse
            </div>

            <aside class="store-soft-panel h-fit rounded-lg p-5">
                @auth
                    @if ($defaultReviewItem)
                        <h3 class="text-sm font-bold text-stone-800">{{ $existingReview ? 'Edite sua avaliação' : 'Avalie sua compra' }}</h3>
                        <p class="mt-1 text-xs leading-5 text-stone-500">Sua opinião será identificada como compra verificada.</p>
                        <form action="{{ route('product.reviews.store', $product) }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            @if ($eligibleReviewItems->count() > 1)
                                <div>
                                    <label for="review-order-item" class="mb-1 block text-xs font-bold">Compra</label>
                                    <select id="review-order-item" name="order_item_id" class="store-input w-full text-xs">
                                        @foreach ($eligibleReviewItems as $item)
                                            <option value="{{ $item->id }}" @selected(old('order_item_id', $defaultReviewItem->id) == $item->id)>Pedido #{{ $item->order_id }} · {{ $item->color_snapshot }} · {{ $item->size_snapshot }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="order_item_id" value="{{ $defaultReviewItem->id }}">
                            @endif

                            <fieldset>
                                <legend class="mb-2 text-xs font-bold">Sua nota</legend>
                                <div class="flex flex-row-reverse justify-end gap-1 review-rating">
                                    @for ($rating = 5; $rating >= 1; $rating--)
                                        <input class="peer sr-only" type="radio" name="rating" id="rating-{{ $rating }}" value="{{ $rating }}" @checked((int) old('rating', $existingReview?->rating) === $rating) required>
                                        <label for="rating-{{ $rating }}" class="cursor-pointer text-2xl text-stone-300 transition" aria-label="{{ $rating }} estrelas">★</label>
                                    @endfor
                                </div>
                                @error('rating')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </fieldset>

                            <div>
                                <label for="review-title" class="mb-1 block text-xs font-bold">Título <span class="font-normal text-stone-400">(opcional)</span></label>
                                <input id="review-title" name="title" maxlength="80" value="{{ old('title', $existingReview?->title) }}" class="store-input w-full text-xs" placeholder="Resuma sua experiência">
                                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="review-comment" class="mb-1 block text-xs font-bold">Sua avaliação</label>
                                <textarea id="review-comment" name="comment" minlength="10" maxlength="1000" rows="5" required class="store-input w-full text-xs" placeholder="Conte sobre tecido, tamanho e caimento">{{ old('comment', $existingReview?->comment) }}</textarea>
                                @error('comment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <button class="store-button store-button-primary w-full" type="submit">{{ $existingReview ? 'Atualizar avaliação' : 'Publicar avaliação' }}</button>
                        </form>
                    @else
                        <h3 class="text-sm font-bold text-stone-800">Avaliações de clientes</h3>
                        <p class="mt-2 text-xs leading-5 text-stone-500">A avaliação é liberada quando um pedido com este produto é marcado como entregue.</p>
                    @endif
                @else
                    <h3 class="text-sm font-bold text-stone-800">Comprou este produto?</h3>
                    <p class="mt-2 text-xs leading-5 text-stone-500">Entre na sua conta para avaliar uma compra entregue.</p>
                    <a href="{{ route('login') }}" class="store-button store-button-outline mt-4 w-full">Entrar para avaliar</a>
                @endauth
            </aside>
        </div>
    </div>
</section>
