<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportOctober2026Collection extends Command
{
    protected $signature = 'catalog:import-october-2026 {--force : Permite executar fora de produção}';

    protected $description = 'Importa de forma idempotente a coleção curada de outubro de 2026';

    public function handle(): int
    {
        if (! app()->environment('production') && ! $this->option('force')) {
            $this->error('Use --force para importar fora de produção.');

            return self::FAILURE;
        }

        $assetDirectory = database_path('seeders/assets/colecao-outubro-2026');
        $disk = Storage::disk(config('filesystems.default'));

        foreach ($this->catalog() as $item) {
            DB::transaction(function () use ($item, $assetDirectory, $disk): void {
                $category = Category::query()->firstOrCreate(
                    ['slug' => $item['category_slug']],
                    ['name' => $item['category']],
                );

                $product = Product::query()->updateOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'active' => true,
                    ],
                );

                $variantIds = [];
                foreach ($item['variants'] as [$color, $size, $stock]) {
                    $variantIds[] = $product->variants()->updateOrCreate(
                        ['color' => $color, 'size' => $size],
                        ['stock' => $stock],
                    )->id;
                }
                $product->variants()->whereNotIn('id', $variantIds)->delete();

                $imageIds = [];
                foreach ($item['images'] as $image) {
                    $source = $assetDirectory.DIRECTORY_SEPARATOR.$image;
                    if (! is_file($source)) {
                        throw new \RuntimeException("Imagem ausente: {$image}");
                    }

                    $target = 'products/'.$image;
                    if (! $disk->exists($target)) {
                        $stream = fopen($source, 'rb');
                        try {
                            if (! $disk->put($target, $stream)) {
                                throw new \RuntimeException("Falha ao enviar {$image}");
                            }
                        } finally {
                            if (is_resource($stream)) {
                                fclose($stream);
                            }
                        }
                    }

                    $imageIds[] = $product->images()->firstOrCreate(['image' => $image])->id;
                }
                $product->images()->whereNotIn('id', $imageIds)->delete();
            });

            $this->line("Importado: {$item['name']}");
        }

        $this->info(count($this->catalog()).' produtos da coleção importados com sucesso.');

        return self::SUCCESS;
    }

    /** @return array<int, array<string, mixed>> */
    private function catalog(): array
    {
        $grades = static fn (array $colors, array $sizes = ['M', 'G', 'GG']): array => collect($colors)
            ->flatMap(fn (string $color) => collect($sizes)->map(fn (string $size, int $index) => [$color, $size, 12 - ($index * 2)]))
            ->all();

        return [
            ['slug' => 'conjunto-pantalona-essenza', 'name' => 'Conjunto Pantalona Essenza', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '129.90', 'description' => 'Conjunto feminino de camisa curta com botões e calça pantalona de cintura alta. Uma proposta elegante e confortável para produções do dia à noite.', 'images' => ['conjunto-essenza-vermelho.webp', 'conjunto-essenza-verde-militar.webp'], 'variants' => $grades(['Vermelho', 'Verde Militar'])],
            ['slug' => 'conjunto-listrado-riviera', 'name' => 'Conjunto Listrado Riviera', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '129.90', 'description' => 'Conjunto de três peças com cropped, calça pantalona e camisa alongada. As listras verticais valorizam a silhueta e deixam a composição sofisticada.', 'images' => ['conjunto-riviera-modelo.webp', 'conjunto-riviera-cores.webp'], 'variants' => $grades(['Off-white e Preto', 'Off-white e Azul', 'Bege e Marrom'])],
            ['slug' => 'camisa-aurora', 'name' => 'Camisa Aurora', 'category' => 'Blusas', 'category_slug' => 'blusas', 'price' => '79.90', 'description' => 'Camisa feminina de manga longa, modelagem leve e fechamento frontal por botões. Versátil para composições casuais ou elegantes.', 'images' => ['camisa-aurora-lima.webp', 'camisa-aurora-azul.webp'], 'variants' => $grades(['Verde Lima', 'Azul Serenity'])],
            ['slug' => 'conjunto-saia-lumiere', 'name' => 'Conjunto Saia Longa Lumière', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '119.90', 'description' => 'Conjunto monocromático com camisa de amarração e saia longa fluida com fenda lateral. Leve, delicado e ideal para dias especiais.', 'images' => ['conjunto-lumiere.webp'], 'variants' => $grades(['Amarelo Manteiga'])],
            ['slug' => 'saia-longa-poa-solar', 'name' => 'Saia Longa Poá Solar', 'category' => 'Saias', 'category_slug' => 'saias', 'price' => '89.90', 'description' => 'Saia longa amarela com estampa de poás brancos, bolsos e recorte franzido. Uma peça alegre e fluida para looks de verão.', 'images' => ['saia-poa-solar.webp'], 'variants' => $grades(['Amarelo'])],
            ['slug' => 'conjunto-tropical-brisa', 'name' => 'Conjunto Tropical Brisa', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '119.90', 'description' => 'Conjunto estampado com cropped de alças e calça pantalona. A folhagem marcante e o caimento amplo criam um visual fresco e elegante.', 'images' => ['conjunto-brisa-verde.webp', 'conjunto-brisa-azul.webp'], 'variants' => $grades(['Verde Folhagem', 'Bege e Azul'])],
            ['slug' => 'conjunto-cetim-champagne', 'name' => 'Conjunto Cetim Champagne', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '129.90', 'description' => 'Conjunto acetinado com cropped de alças e amarração nas costas, acompanhado de saia longa fluida. Sofisticação com brilho delicado.', 'images' => ['conjunto-champagne.webp'], 'variants' => $grades(['Champagne'])],
            ['slug' => 'saia-longa-folhagem', 'name' => 'Saia Longa Folhagem', 'category' => 'Saias', 'category_slug' => 'saias', 'price' => '89.90', 'description' => 'Saia longa estampada com cintura alta e detalhe transpassado. Caimento leve para composições coloridas e femininas.', 'images' => ['saia-folhagem-rosa.webp', 'saia-folhagem-verde.webp', 'saia-folhagem-cores.webp'], 'variants' => $grades(['Rosa', 'Verde', 'Lilás', 'Amarelo'])],
            ['slug' => 'conjunto-pantalona-belle', 'name' => 'Conjunto Pantalona Belle', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '114.90', 'description' => 'Conjunto rosa de camisa ampla e calça pantalona, com modelagem descontraída e elegante para um visual monocromático moderno.', 'images' => ['conjunto-belle-rosa.webp'], 'variants' => $grades(['Rosa'])],
            ['slug' => 'camisa-ampla-nuvem', 'name' => 'Camisa Ampla Nuvem', 'category' => 'Blusas', 'category_slug' => 'blusas', 'price' => '74.90', 'description' => 'Camisa branca de modelagem ampla, mangas três quartos e acabamento leve. Um clássico versátil para diferentes ocasiões.', 'images' => ['camisa-nuvem.webp'], 'variants' => $grades(['Branco'], ['M', 'G', 'GG'])],
            ['slug' => 'conjunto-pantalona-manteiga', 'name' => 'Conjunto Pantalona Manteiga', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '124.90', 'description' => 'Conjunto em tom manteiga com camisa de mangas dobradas e calça pantalona de cintura alta. Elegante, luminoso e confortável.', 'images' => ['conjunto-manteiga.webp'], 'variants' => $grades(['Amarelo Manteiga'])],
            ['slug' => 'saia-longa-horizonte', 'name' => 'Saia Longa Horizonte', 'category' => 'Saias', 'category_slug' => 'saias', 'price' => '84.90', 'description' => 'Saia longa verde de cintura alta e caimento amplo. A cor profunda cria uma base marcante para combinações estampadas ou neutras.', 'images' => ['saia-horizonte.webp'], 'variants' => $grades(['Verde Floresta'])],
            ['slug' => 'saia-envelope-rose', 'name' => 'Saia Envelope Rosé', 'category' => 'Saias', 'category_slug' => 'saias', 'price' => '94.90', 'description' => 'Saia longa rosé com efeito envelope e camada assimétrica frontal. Feminina e sofisticada, com movimento delicado.', 'images' => ['saia-envelope-rose.webp'], 'variants' => $grades(['Rosé'])],
            ['slug' => 'saia-longa-jardim-azul', 'name' => 'Saia Longa Jardim Azul', 'category' => 'Saias', 'category_slug' => 'saias', 'price' => '89.90', 'description' => 'Saia longa azul com delicada estampa floral, cintura alta e caimento evasê. Leve e fácil de combinar em produções casuais.', 'images' => ['saia-jardim-azul.webp'], 'variants' => $grades(['Azul Floral'])],
            ['slug' => 'macaquinho-listrado-serena', 'name' => 'Macaquinho Listrado Serena', 'category' => 'Macaquinhos', 'category_slug' => 'macaquinhos', 'price' => '109.90', 'description' => 'Macaquinho listrado com cintura marcada e terceira peça ampla coordenada. Um conjunto leve, moderno e cheio de movimento.', 'images' => ['macaquinho-serena-rosa.webp', 'macaquinho-serena-azul.webp'], 'variants' => $grades(['Rosa', 'Azul Claro'])],
            ['slug' => 'conjunto-tres-pecas-alva', 'name' => 'Conjunto Três Peças Alva', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '119.90', 'description' => 'Conjunto branco com cropped, shorts de alfaiataria com bolsos e terceira peça alongada. Uma composição fresca e refinada.', 'images' => ['conjunto-alva.webp'], 'variants' => $grades(['Branco'])],
            ['slug' => 'conjunto-sandy', 'name' => 'Conjunto Sandy', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '189.99', 'description' => 'Conjunto feminino de camisa de manga longa com bolso frontal e calça pantalona de cintura alta. O caimento elegante e confortável cria uma produção completa para diferentes ocasiões.', 'images' => ['conjunto-sandy-preto-portrait.webp', 'conjunto-sandy-marrom-portrait.webp', 'conjunto-sandy-azul-portrait.webp', 'conjunto-sandy-amarelo-portrait.webp', 'conjunto-sandy-rosa-portrait.webp', 'conjunto-sandy-natural-portrait.webp'], 'variants' => $grades(['Preto', 'Marrom', 'Azul Royal', 'Amarelo', 'Rosa', 'Natural'])],
            ['slug' => 'conjunto-maite', 'name' => 'Conjunto Maitê', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '199.99', 'description' => 'Conjunto feminino com blusa transpassada de mangas longas, amarração lateral e calça pantalona de cintura alta. O caimento elegante valoriza a silhueta sem abrir mão do conforto.', 'images' => ['conjunto-maite-bordo-portrait.webp', 'conjunto-maite-azul-portrait.webp', 'conjunto-maite-natural-portrait.webp', 'conjunto-maite-marrom-portrait.webp'], 'variants' => $grades(['Bordô', 'Azul Royal', 'Natural', 'Marrom'])],
            ['slug' => 'conjunto-lorena', 'name' => 'Conjunto Lorena', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '80.00', 'description' => 'Conjunto feminino em linho com elastano, composto por regata de alças finas e calça de cintura alta com cinto faixa. Leve, confortável e versátil para produções casuais ou elegantes.', 'images' => ['conjunto-lorena-natural-portrait.webp', 'conjunto-lorena-azul-royal-portrait.webp', 'conjunto-lorena-bordo-portrait.webp', 'conjunto-lorena-preto-portrait.webp', 'conjunto-lorena-marrom-portrait.webp', 'conjunto-lorena-rosa-portrait.webp', 'conjunto-lorena-verde-menta-portrait.webp', 'conjunto-lorena-azul-claro-portrait.webp'], 'variants' => $grades(['Natural', 'Azul Royal', 'Bordô', 'Preto', 'Marrom', 'Rosa', 'Verde Menta', 'Azul Claro'])],
            ['slug' => 'conjunto-stella', 'name' => 'Conjunto Stella', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '80.00', 'description' => 'Conjunto feminino em linho com elastano, composto por camisa de mangas longas e shorts de cintura alta. Fresco e versátil, combina conforto com um visual elegante para os dias mais quentes.', 'images' => ['conjunto-stella-amarelo-manteiga-portrait.webp', 'conjunto-stella-verde-menta-portrait.webp', 'conjunto-stella-azul-marinho-portrait.webp', 'conjunto-stella-preto-portrait.webp', 'conjunto-stella-azul-claro-portrait.webp', 'conjunto-stella-rosa-portrait.webp', 'conjunto-stella-natural-portrait.webp'], 'variants' => $grades(['Amarelo Manteiga', 'Verde Menta', 'Azul Marinho', 'Preto', 'Azul Claro', 'Rosa', 'Natural'], ['M', 'G'])],
            ['slug' => 'vestido-agata', 'name' => 'Vestido Ágata', 'category' => 'Vestidos', 'category_slug' => 'vestidos', 'price' => '80.00', 'description' => 'Vestido curto de um ombro só, com manga longa ampla, faixa para amarração na cintura e saia em camadas fluidas. Leve e elegante para ocasiões especiais.', 'images' => ['vestido-agata-amarelo-portrait.webp', 'vestido-agata-preto-portrait.webp', 'vestido-agata-verde-menta-portrait.webp', 'vestido-agata-azul-claro-portrait.webp'], 'variants' => $grades(['Amarelo', 'Preto', 'Verde Menta', 'Azul Claro'])],
            ['slug' => 'conjunto-iris', 'name' => 'Conjunto Íris', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '99.90', 'description' => 'Conjunto feminino em tecido sensorial, composto por colete de alfaiataria sem mangas com fechamento frontal e calça de cintura alta. Uma combinação moderna, confortável e elegante para diferentes ocasiões.', 'images' => ['conjunto-iris-azul-claro-portrait.webp', 'conjunto-iris-preto-portrait.webp', 'conjunto-iris-bordo-portrait.webp', 'conjunto-iris-verde-menta-portrait.webp', 'conjunto-iris-rosa-portrait.webp'], 'variants' => $grades(['Azul Claro', 'Preto', 'Bordô', 'Verde Menta', 'Rosa'], ['M', 'G'])],
            ['slug' => 'conjunto-zara', 'name' => 'Conjunto Zara', 'category' => 'Conjuntos', 'category_slug' => 'conjuntos', 'price' => '109.90', 'description' => 'Conjunto feminino em linho com elastano, composto por blusa cropped de mangas curtas e calça pantalona de cintura alta. O caimento amplo combina conforto, frescor e elegância para diferentes ocasiões.', 'images' => ['conjunto-zara-azul-petroleo-portrait.webp', 'conjunto-zara-bordo-portrait.webp', 'conjunto-zara-marrom-portrait.webp', 'conjunto-zara-azul-marinho-portrait.webp', 'conjunto-zara-amarelo-portrait.webp', 'conjunto-zara-vermelho-portrait.webp', 'conjunto-zara-rosa-portrait.webp', 'conjunto-zara-natural-portrait.webp', 'conjunto-zara-azul-claro-portrait.webp', 'conjunto-zara-verde-menta-portrait.webp'], 'variants' => $grades(['Azul Petróleo', 'Bordô', 'Marrom', 'Azul Marinho', 'Amarelo', 'Vermelho', 'Rosa', 'Natural', 'Azul Claro', 'Verde Menta'])],
        ];
    }
}
