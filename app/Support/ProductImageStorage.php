<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductImageStorage
{
    public function store(UploadedFile $image): string
    {
        $name = Str::ulid().'.webp';
        $contents = $this->reencode($image);

        if (! Storage::disk($this->disk())->put($this->path($name), $contents, ['visibility' => 'public'])) {
            throw new RuntimeException('Não foi possível armazenar a imagem do produto.');
        }

        return $name;
    }

    public function delete(string $name): bool
    {
        return Storage::disk($this->disk())->delete($this->path($name));
    }

    public function url(?string $name): string
    {
        return $name ? Storage::disk($this->disk())->url($this->path($name)) : '';
    }

    public function disk(): string
    {
        return (string) config('filesystems.default', 'public');
    }

    private function path(string $name): string
    {
        return 'products/'.ltrim($name, '/');
    }

    private function reencode(UploadedFile $image): string
    {
        $source = match ($image->getMimeType()) {
            'image/jpeg' => imagecreatefromjpeg($image->getRealPath()),
            'image/png' => imagecreatefrompng($image->getRealPath()),
            'image/webp' => imagecreatefromwebp($image->getRealPath()),
            default => false,
        };

        if ($source === false) {
            throw new RuntimeException('A imagem enviada não pôde ser processada.');
        }

        ob_start();
        $encoded = imagewebp($source, null, 88);
        $contents = ob_get_clean();
        imagedestroy($source);

        if (! $encoded || ! is_string($contents) || $contents === '') {
            throw new RuntimeException('A imagem enviada não pôde ser convertida com segurança.');
        }

        return $contents;
    }
}
