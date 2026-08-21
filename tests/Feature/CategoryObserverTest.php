<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_unique_slug_when_a_category_is_created_without_one(): void
    {
        $first = Category::create(['name' => 'Vestidos de Festa']);
        $second = Category::create(['name' => 'Vestidos de Festa']);

        $this->assertSame('vestidos-de-festa', $first->slug);
        $this->assertSame('vestidos-de-festa-2', $second->slug);
    }

    public function test_it_preserves_the_generated_slug_when_the_name_changes(): void
    {
        $category = Category::create(['name' => 'Blusas']);

        $category->update(['name' => 'Blusas de Linho']);

        $this->assertSame('blusas', $category->fresh()->slug);
    }

    public function test_it_preserves_a_manually_edited_slug(): void
    {
        $category = Category::create(['name' => 'Calças', 'slug' => 'calcas-especiais']);

        $category->update(['name' => 'Calças Jeans']);

        $this->assertSame('calcas-especiais', $category->fresh()->slug);
    }
}
