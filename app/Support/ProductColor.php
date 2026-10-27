<?php

namespace App\Support;

use Illuminate\Support\Str;

class ProductColor
{
    public static function css(string $name): string
    {
        $normalized = Str::of($name)->ascii()->lower()->toString();

        foreach (self::palette() as $needle => $color) {
            if (str_contains($normalized, $needle)) {
                return $color;
            }
        }

        return '#d6c8c3';
    }

    /** @return array<string, string> */
    private static function palette(): array
    {
        return [
            'vermelho' => '#c92134', 'rose' => '#d993a4', 'rosa' => '#e78eae',
            'verde militar' => '#384936', 'verde floresta' => '#174b35', 'verde' => '#2d8b65',
            'azul serenity' => '#8bbbe5', 'azul claro' => '#b8d9ef', 'azul' => '#3159a7',
            'amarelo manteiga' => '#f5ed9b', 'amarelo' => '#f1d348', 'champagne' => '#dbc49c',
            'lilas' => '#a982c5', 'branco' => '#f8f8f5', 'off-white' => '#eee9dc',
            'bege' => '#cdb58f', 'marrom' => '#76523b', 'preto' => '#24201f',
            'natural' => '#ddd3bd',
        ];
    }
}
