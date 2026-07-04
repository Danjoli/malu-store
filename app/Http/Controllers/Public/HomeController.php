<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Public\Shop\ProductFilterService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productFilterService;

    public function __construct(ProductFilterService $productFilterService)
    {
        $this->productFilterService = $productFilterService;
    }

    public function index(Request $request)
    {
        $products = $this->productFilterService->filter($request);

        return view('public.home.index', compact('products'));
    }
}
