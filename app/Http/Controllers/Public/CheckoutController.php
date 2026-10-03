<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Checkout\CheckoutRequest;
use App\Services\Public\Checkout\CheckoutService;
use App\Services\Public\Checkout\CheckoutViewService;
use App\Services\Public\Payment\PaymentService;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected PaymentService $paymentService,
    ) {}

    public function processOrder(CheckoutRequest $request)
    {
        $order = $this->checkoutService->process(
            $request->validated()
        );

        return match ($request->validated('payment_method')) {
            'pix' => $this->paymentService->pix($order->id),
            'boleto' => $this->paymentService->boleto($order->id),
            'card' => $this->paymentService->cardCheckout($order),
        };
    }

    public function index(CheckoutViewService $service)
    {
        return view('public.checkout.index', $service->getData());
    }
}
