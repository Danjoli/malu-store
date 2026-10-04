<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImageStorage
{
    public function store(UploadedFile $image): string
    {
        $name = Str::ulid().'.'.strtolower($image->extension());
        $image->storeAs('products', $name, $this->disk());

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
}
