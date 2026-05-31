<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Product;
use App\Models\Category;

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
        // URLs fixas do site
        $urls = [
            url('/'),
            url('/produtos'),
            url('/categorias'),
            url('/sobre'),
            url('/contato'),
            url('/policy'),
            url('/terms'),
            url('/privacy'),
        ];

        // CATEGORIAS (tem slug ✔)
        $categories = Category::all();

        foreach ($categories as $category) {
            $urls[] = url("/categoria/{$category->slug}");
        }

        // PRODUTOS (SEM SLUG → usa ID)
        $products = Product::where('active', 1)->get();

        foreach ($products as $product) {
            $urls[] = url("/produto/{$product->id}");
        }

        // Início do XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Monta URLs
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url, ENT_XML1, 'UTF-8') . '</loc>';
            $xml .= '<lastmod>' . now()->toDateString() . '</lastmod>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        File::put(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap gerado com sucesso!');
    }
}
