<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class SeedVolumeTestData extends Command
{
    protected $signature = 'staging:seed-volume
        {--users=10000 : Total desejado de usuarios sinteticos}
        {--products=10000 : Total desejado de produtos sinteticos}
        {--batch=1000 : Registros gravados por lote}
        {--confirm-staging : Confirma a alteracao do banco de homologacao}';

    protected $description = 'Popula a homologacao com dados sinteticos em lotes, sem acionar integracoes externas';

    public function handle(): int
    {
        if (! app()->environment(['staging', 'local', 'testing'])) {
            $this->error('Comando bloqueado: permitido somente em staging, local ou testing.');

            return self::FAILURE;
        }

        if (app()->environment('staging') && ! $this->option('confirm-staging')) {
            $this->error('Use --confirm-staging para confirmar a carga na homologacao.');

            return self::FAILURE;
        }

        $users = $this->validatedIntegerOption('users', 0, 1_000_000);
        $products = $this->validatedIntegerOption('products', 0, 1_000_000);
        $batchSize = $this->validatedIntegerOption('batch', 100, 5_000);

        if ($users === null || $products === null || $batchSize === null) {
            return self::FAILURE;
        }

        DB::disableQueryLog();

        $this->info(sprintf(
            'Ambiente: %s | banco: %s | alvo: %s usuarios e %s produtos',
            app()->environment(),
            (string) DB::connection()->getDatabaseName(),
            number_format($users, 0, ',', '.'),
            number_format($products, 0, ',', '.')
        ));

        try {
            $startedAt = microtime(true);
            $this->seedUsers($users, $batchSize);
            $this->seedProducts($products, $batchSize);
            $this->reportBenchmarks();

            $this->newLine();
            $this->info('Carga concluida em '.$this->formatDuration(microtime(true) - $startedAt).'.');
        } catch (Throwable $exception) {
            report($exception);
            $this->error('Carga interrompida: '.$exception->getMessage());
            $this->warn('O comando e retomavel: corrija o problema e execute novamente com os mesmos alvos.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function seedUsers(int $target, int $batchSize): void
    {
        $existing = DB::table('users')->where('email', 'like', 'volume-%@example.invalid')->count();
        $remaining = max(0, $target - $existing);

        $this->components->task('Usuarios sinteticos existentes: '.number_format($existing, 0, ',', '.'));

        if ($remaining === 0) {
            return;
        }

        $password = Hash::make(Str::random(40));
        $progress = $this->output->createProgressBar($remaining);
        $progress->start();

        for ($offset = $existing + 1; $offset <= $target; $offset += $batchSize) {
            $end = min($offset + $batchSize - 1, $target);
            $now = now();
            $rows = [];

            for ($number = $offset; $number <= $end; $number++) {
                $identifier = str_pad((string) $number, 9, '0', STR_PAD_LEFT);
                $rows[] = [
                    'name' => 'Cliente Volume '.$identifier,
                    'email' => 'volume-'.$identifier.'@example.invalid',
                    'email_verified_at' => $now,
                    'password' => $password,
                    'phone' => null,
                    'asaas_customer_id' => null,
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users')->insertOrIgnore($rows);
            $progress->advance(count($rows));
            unset($rows);
        }

        $progress->finish();
        $this->newLine();
    }

    private function seedProducts(int $target, int $batchSize): void
    {
        $categoryId = DB::table('categories')->where('slug', 'volume-test')->value('id');

        if (! $categoryId) {
            DB::table('categories')->insertOrIgnore([
                'name' => 'Teste de Volume',
                'slug' => 'volume-test',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoryId = DB::table('categories')->where('slug', 'volume-test')->value('id');
        }

        $existing = DB::table('products')->where('slug', 'like', 'volume-produto-%')->count();
        $remaining = max(0, $target - $existing);

        $this->components->task('Produtos sinteticos existentes: '.number_format($existing, 0, ',', '.'));

        if ($remaining === 0) {
            return;
        }

        $progress = $this->output->createProgressBar($remaining);
        $progress->start();

        for ($offset = $existing + 1; $offset <= $target; $offset += $batchSize) {
            $end = min($offset + $batchSize - 1, $target);
            $now = now();
            $products = [];
            $slugs = [];

            for ($number = $offset; $number <= $end; $number++) {
                $identifier = str_pad((string) $number, 9, '0', STR_PAD_LEFT);
                $slug = 'volume-produto-'.$identifier;
                $slugs[] = $slug;
                $products[] = [
                    'category_id' => $categoryId,
                    'name' => 'Produto de Teste '.$identifier,
                    'slug' => $slug,
                    'description' => 'Registro sintetico para teste de volume da homologacao.',
                    'price' => number_format(49.90 + ($number % 250), 2, '.', ''),
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('products')->insertOrIgnore($products);

            $productIds = DB::table('products')->whereIn('slug', $slugs)->pluck('id');
            $variants = $productIds->map(fn (int $productId): array => [
                'product_id' => $productId,
                'color' => 'Teste',
                'size' => 'U',
                'stock' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            DB::table('product_variants')->insertOrIgnore($variants);
            $progress->advance(count($products));
            unset($products, $variants, $slugs, $productIds);
        }

        $progress->finish();
        $this->newLine();
    }

    private function reportBenchmarks(): void
    {
        $measure = function (callable $query): string {
            $startedAt = microtime(true);
            $query();

            return number_format((microtime(true) - $startedAt) * 1_000, 2, ',', '.').' ms';
        };

        $this->newLine();
        $this->table(['Consulta', 'Tempo'], [
            ['Contagem de usuarios', $measure(fn () => DB::table('users')->count())],
            ['Contagem de catalogo disponivel', $measure(fn () => DB::table('products')->where('active', true)->whereExists(
                fn ($query) => $query->selectRaw('1')->from('product_variants')->whereColumn('product_variants.product_id', 'products.id')->where('stock', '>', 0)
            )->count())],
            ['Primeira pagina do catalogo', $measure(fn () => DB::table('products')->where('active', true)->latest('id')->limit(24)->get())],
        ]);
    }

    private function validatedIntegerOption(string $name, int $minimum, int $maximum): ?int
    {
        $value = filter_var($this->option($name), FILTER_VALIDATE_INT);

        if ($value === false || $value < $minimum || $value > $maximum) {
            $this->error("--{$name} deve ser um numero inteiro entre {$minimum} e {$maximum}.");

            return null;
        }

        return $value;
    }

    private function formatDuration(float $seconds): string
    {
        if ($seconds < 60) {
            return number_format($seconds, 1, ',', '.').' segundos';
        }

        return number_format($seconds / 60, 1, ',', '.').' minutos';
    }
}
