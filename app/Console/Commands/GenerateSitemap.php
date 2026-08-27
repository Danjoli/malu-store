<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera o sitemap.xml automaticamente da loja Malu Store';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mantém somente rotas públicas que existem na aplicação.
        $urls = [
            ['loc' => route('home'), 'lastmod' => now()],
            ['loc' => route('catalog.index'), 'lastmod' => now()],
            ['loc' => route('policy'), 'lastmod' => now()],
            ['loc' => route('terms'), 'lastmod' => now()],
            ['loc' => route('privacy'), 'lastmod' => now()],
        ];

        $categories = Category::query()->get();

        foreach ($categories as $category) {
            $urls[] = [
                'loc' => route('catalog.index', ['category' => $category->slug]),
                'lastmod' => $category->updated_at,
            ];
        }

        $products = Product::query()->where('active', true)->get();

        foreach ($products as $product) {
            $urls[] = [
                'loc' => route('product.show', $product),
                'lastmod' => $product->updated_at,
            ];
        }

        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($urls as $url) {
            $xml[] = '    <url>';
            $xml[] = '        <loc>'.htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8').'</loc>';
            $xml[] = '        <lastmod>'.$url['lastmod']->toDateString().'</lastmod>';
            $xml[] = '    </url>';
        }

        $xml[] = '</urlset>';

        File::put(public_path('sitemap.xml'), implode(PHP_EOL, $xml).PHP_EOL);

        $this->info('Sitemap gerado com sucesso!');
    }
}
