<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;

return [
    'attributes' => [
        Admin::class => ['name', 'email', 'role', 'is_active'],
        Category::class => ['name', 'slug'],
        Product::class => ['category_id', 'name', 'slug', 'description', 'price', 'active'],
        ProductVariant::class => ['product_id', 'color', 'size', 'stock'],
        Shipment::class => [
            'order_id', 'shipment_id', 'carrier', 'tracking_code', 'shipping_cost',
            'service_id', 'status', 'label_url', 'last_update', 'shipped_at', 'delivered_at',
        ],
    ],
    'sensitive_attributes' => [
        Admin::class => ['password'],
    ],
];
