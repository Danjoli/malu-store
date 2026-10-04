<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MigrateProductImages extends Command
{
    protected $signature = 'storage:migrate-products
        {--from=public : Source filesystem disk}
        {--to=s3 : Destination filesystem disk}
        {--execute : Copy files; without this option the command is a dry run}';

    protected $description = 'Copy and verify product images without deleting the source files';

    public function handle(): int
    {
        $source = Storage::disk((string) $this->option('from'));
        $destination = Storage::disk((string) $this->option('to'));
        $files = $source->allFiles('products');
        $execute = (bool) $this->option('execute');

        $this->info(sprintf('%s %d file(s).', $execute ? 'Migrating' : 'Would migrate', count($files)));

        if (! $execute) {
            $this->comment('Dry run only. Re-run with --execute to copy and verify.');

            return self::SUCCESS;
        }

        foreach ($files as $file) {
            $stream = $source->readStream($file);

            if (! is_resource($stream)) {
                throw new RuntimeException("Unable to read {$file}.");
            }

            try {
                if (! $destination->writeStream($file, $stream)) {
                    throw new RuntimeException("Unable to write {$file}.");
                }
            } finally {
                fclose($stream);
            }

            if ($this->checksum($source, $file) !== $this->checksum($destination, $file)) {
                throw new RuntimeException("Checksum mismatch for {$file}.");
            }

            $this->line("Verified: {$file}");
        }

        $this->info('Migration verified. Source files were not deleted.');

        return self::SUCCESS;
    }

    private function checksum(FilesystemAdapter $disk, string $file): string
    {
        $stream = $disk->readStream($file);

        if (! is_resource($stream)) {
            throw new RuntimeException("Unable to verify {$file}.");
        }

        try {
            $context = hash_init('sha256');
            hash_update_stream($context, $stream);

            return hash_final($context);
        } finally {
            fclose($stream);
        }
    }
}
