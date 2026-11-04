<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                $content = $this->productContent()[$item['slug']];
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
                        'details' => $content['details'],
                        'composition' => $content['composition'],
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

        $this->seedDemoReviews();

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

    /** @return array<string, array{details: string, composition: string}> */
    private function productContent(): array
    {
        $labelNotice = 'Os percentuais exatos podem variar conforme o lote. Confira a etiqueta interna da peça antes da lavagem.';

        return [
            'conjunto-pantalona-essenza' => ['details' => 'Conjunto de duas peças. Camisa curta com gola, mangas curtas e fechamento frontal por botões. Calça pantalona de cintura alta, cós confortável e pernas amplas.', 'composition' => "Tecido plano de toque leve. {$labelNotice}"],
            'conjunto-listrado-riviera' => ['details' => 'Conjunto coordenado de três peças: cropped, camisa alongada e calça pantalona. As listras verticais e a modelagem ampla alongam visualmente a silhueta.', 'composition' => "Tecido plano listrado, leve e sem transparência aparente. {$labelNotice}"],
            'camisa-aurora' => ['details' => 'Camisa de modelagem solta, gola clássica, mangas longas com punhos e fechamento frontal por botões. Comprimento versátil para usar por dentro ou por fora da parte de baixo.', 'composition' => "Tecido plano leve e de caimento fluido. {$labelNotice}"],
            'conjunto-saia-lumiere' => ['details' => 'Conjunto com camisa cropped de amarração frontal e saia longa de cintura alta. A saia possui caimento amplo e fenda lateral para facilitar os movimentos.', 'composition' => "Tecido leve, macio e fluido. {$labelNotice}"],
            'saia-longa-poa-solar' => ['details' => 'Saia longa de cintura alta, bolsos laterais e recorte franzido próximo à barra. Modelagem evasê com bastante movimento.', 'composition' => "Tecido plano estampado de toque leve. {$labelNotice}"],
            'conjunto-tropical-brisa' => ['details' => 'Conjunto de cropped com alças finas e calça pantalona de cintura alta. Modelagem ampla e estampa de folhagens em destaque.', 'composition' => "Tecido plano estampado, leve e fluido. {$labelNotice}"],
            'conjunto-cetim-champagne' => ['details' => 'Conjunto de cropped com alças e amarração nas costas, acompanhado de saia longa de cintura alta. Superfície acetinada com brilho delicado.', 'composition' => "Tecido de aspecto acetinado e toque macio. {$labelNotice}"],
            'saia-longa-folhagem' => ['details' => 'Saia longa de cintura alta, caimento evasê e sobreposição frontal com efeito transpassado. Estampa de folhagens em diferentes combinações de cores.', 'composition' => "Tecido plano estampado, leve e fluido. {$labelNotice}"],
            'conjunto-pantalona-belle' => ['details' => 'Conjunto monocromático com camisa de modelagem ampla e calça pantalona de cintura alta. Caimento solto para uma produção elegante e confortável.', 'composition' => "Tecido plano leve, com caimento fluido. {$labelNotice}"],
            'camisa-ampla-nuvem' => ['details' => 'Camisa alongada de modelagem ampla, gola clássica, mangas três quartos com punhos largos e fechamento frontal por botões.', 'composition' => "Tecido plano leve em tonalidade branca. {$labelNotice}"],
            'conjunto-pantalona-manteiga' => ['details' => 'Conjunto com camisa de mangas ajustáveis e calça pantalona de cintura alta. Modelagem elegante em tonalidade amarelo-manteiga.', 'composition' => "Tecido plano leve e de caimento confortável. {$labelNotice}"],
            'saia-longa-horizonte' => ['details' => 'Saia longa de cintura alta e modelagem evasê. O corte amplo oferece movimento e facilita combinações com blusas lisas ou estampadas.', 'composition' => "Tecido plano de caimento encorpado e confortável. {$labelNotice}"],
            'saia-envelope-rose' => ['details' => 'Saia longa de cintura alta, sobreposição frontal e barra assimétrica com efeito envelope. Modelagem fluida e feminina.', 'composition' => "Tecido plano leve e com bom movimento. {$labelNotice}"],
            'saia-longa-jardim-azul' => ['details' => 'Saia longa de cintura alta, corte evasê e estampa floral delicada. Modelagem ampla para acompanhar os movimentos.', 'composition' => "Tecido plano estampado de toque leve. {$labelNotice}"],
            'macaquinho-listrado-serena' => ['details' => 'Macaquinho de alças, cintura marcada e shorts de comprimento curto, acompanhado de terceira peça ampla coordenada.', 'composition' => "Tecido plano listrado e leve. {$labelNotice}"],
            'conjunto-tres-pecas-alva' => ['details' => 'Conjunto de três peças composto por cropped, shorts de alfaiataria com bolsos e terceira peça alongada. Modelagem fresca e coordenada.', 'composition' => "Tecido plano de aspecto natural e toque leve. {$labelNotice}"],
            'conjunto-sandy' => ['details' => 'Conjunto de duas peças. Camisa de manga longa com gola, fechamento frontal por botões e bolso aplicado. Calça pantalona de cintura alta, cós com passantes e pernas amplas.', 'composition' => "Tecido plano de alfaiataria leve. O fornecedor não informou os percentuais da fibra. {$labelNotice}"],
            'conjunto-maite' => ['details' => 'Conjunto de duas peças. Blusa transpassada de mangas longas, decote em V e amarração lateral ajustável. Calça pantalona de cintura alta e pernas amplas.', 'composition' => "Tecido plano de toque macio e caimento fluido. O fornecedor não informou os percentuais da fibra. {$labelNotice}"],
            'conjunto-lorena' => ['details' => 'Conjunto de duas peças. Regata de alças finas e decote reto, acompanhada de calça de cintura alta, comprimento cropped, bolsos e cinto faixa removível.', 'composition' => "Linho com elastano, conforme informação do fornecedor. {$labelNotice}"],
            'conjunto-stella' => ['details' => 'Conjunto de duas peças. Camisa de mangas longas com fechamento frontal por botões e shorts de cintura alta com bolsos laterais.', 'composition' => "Linho com elastano, conforme informação do fornecedor. {$labelNotice}"],
            'vestido-agata' => ['details' => 'Vestido curto de um ombro só, com manga longa ampla no lado coberto, faixa removível para marcar a cintura e saia em duas camadas.', 'composition' => "Tecido leve e fluido, com camada externa de aparência delicada. O fornecedor não informou os percentuais da fibra. {$labelNotice}"],
            'conjunto-iris' => ['details' => 'Conjunto de duas peças. Colete sem mangas com decote em V e fechamento frontal por botões, acompanhado de calça de cintura alta e corte reto.', 'composition' => "Tecido sensorial, conforme informação do fornecedor. Os percentuais da fibra não foram informados. {$labelNotice}"],
            'conjunto-zara' => ['details' => 'Conjunto de duas peças. Blusa cropped de mangas curtas e decote redondo, acompanhada de calça pantalona de cintura alta e pernas amplas.', 'composition' => "Linho com elastano, conforme informação do fornecedor. {$labelNotice}"],
        ];
    }

    private function seedDemoReviews(): void
    {
        $reviewers = [
            'ana' => User::query()->firstOrCreate(
                ['email' => 'avaliacao.ana@example.invalid'],
                ['name' => 'Ana (demonstração)', 'password' => Hash::make(str()->random(40))],
            ),
            'beatriz' => User::query()->firstOrCreate(
                ['email' => 'avaliacao.beatriz@example.invalid'],
                ['name' => 'Beatriz (demonstração)', 'password' => Hash::make(str()->random(40))],
            ),
            'carla' => User::query()->firstOrCreate(
                ['email' => 'avaliacao.carla@example.invalid'],
                ['name' => 'Carla (demonstração)', 'password' => Hash::make(str()->random(40))],
            ),
        ];

        $reviews = [
            ['conjunto-sandy', 'ana', 5, 'Elegante e versátil', 'A modelagem pantalona deixou o conjunto elegante, e a camisa combina facilmente com outras peças.'],
            ['conjunto-sandy', 'beatriz', 4, 'Bom caimento', 'Gostei do caimento mais amplo da calça e do acabamento da camisa. A tabela de medidas ajudou na escolha.'],
            ['conjunto-maite', 'carla', 5, 'Modelagem muito bonita', 'A blusa transpassada valoriza a cintura e a amarração lateral permite ajustar melhor ao corpo.'],
            ['conjunto-maite', 'ana', 4, 'Confortável para ocasiões especiais', 'O conjunto tem aparência sofisticada e a calça ampla ficou confortável durante o uso.'],
            ['conjunto-lorena', 'beatriz', 5, 'Leve e confortável', 'O tecido é leve e o cinto faixa ajuda a ajustar a cintura. Gostei bastante dos bolsos.'],
            ['conjunto-stella', 'carla', 4, 'Ótimo para dias quentes', 'O shorts tem um comprimento confortável e a camisa permite usar as mangas dobradas.'],
            ['vestido-agata', 'ana', 5, 'Vestido delicado', 'A manga de um ombro só e as camadas da saia deixam o vestido bonito e com bastante movimento.'],
            ['conjunto-iris', 'beatriz', 5, 'Visual moderno', 'O colete veste bem e também funciona separado da calça. O conjunto ficou moderno e arrumado.'],
            ['conjunto-iris', 'carla', 4, 'Boa combinação', 'Gostei do corte da calça e do fechamento do colete. As duas peças são fáceis de combinar.'],
            ['conjunto-zara', 'ana', 5, 'Confortável e elegante', 'A calça pantalona tem bom movimento e a blusa cropped equilibra bem a proporção do conjunto.'],
        ];

        foreach ($reviews as [$slug, $reviewer, $rating, $title, $comment]) {
            $product = Product::query()->where('slug', $slug)->firstOrFail();
            $user = $reviewers[$reviewer];

            ProductReview::query()->updateOrCreate(
                ['product_id' => $product->id, 'user_id' => $user->id, 'is_demo' => true],
                ['order_item_id' => null, 'rating' => $rating, 'title' => $title, 'comment' => $comment],
            );
        }

        $this->line(count($reviews).' avaliações demonstrativas atualizadas.');
    }
}
