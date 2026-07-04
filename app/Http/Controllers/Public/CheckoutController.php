<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Checkout\CheckoutRequest;
use App\Services\Public\Checkout\CheckoutService;
use App\Services\Public\Checkout\CheckoutViewService;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    public function processOrder(CheckoutRequest $request)
    {
        $order = $this->checkoutService->process(
            $request->validated()
        );

        return redirect()->route('payment', $order->id);
    }

    public function index(CheckoutViewService $service)
    {
        return view('public.checkout.index', $service->getData());
    }
}
