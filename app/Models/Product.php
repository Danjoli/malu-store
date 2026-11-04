<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'details',
        'composition',
        'price',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->oldestOfMany();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Evita que o MySQL materialize toda a tabela de variantes ao verificar estoque.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeInStock(Builder $query, string|array|null $color = null, string|array|null $size = null): Builder
    {
        $conditions = ['pv.product_id = products.id'];
        $bindings = [];

        $colors = array_values(array_filter((array) $color, fn ($value) => is_string($value) && $value !== ''));
        $sizes = array_values(array_filter((array) $size, fn ($value) => is_string($value) && $value !== ''));

        if ($colors !== []) {
            $conditions[] = 'pv.color IN ('.implode(', ', array_fill(0, count($colors), '?')).')';
            array_push($bindings, ...$colors);
        }

        if ($sizes !== []) {
            $conditions[] = 'pv.size IN ('.implode(', ', array_fill(0, count($sizes), '?')).')';
            array_push($bindings, ...$sizes);
        }

        return $query->whereRaw(
            '(SELECT MAX(pv.stock) FROM product_variants pv WHERE '.implode(' AND ', $conditions).') > 0',
            $bindings,
        );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
