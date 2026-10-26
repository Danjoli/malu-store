<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\Domain\PaymentException;
use App\Http\Controllers\Controller;
use App\Http\Presenters\PaymentResponsePresenter;
use App\Http\Requests\Public\Checkout\CheckoutRequest;
use App\Models\Order;
use App\Services\Public\Checkout\CheckoutViewService;
use App\UseCases\Checkout\PlaceOrder;
use App\UseCases\Payment\CreateBoletoPayment;
use App\UseCases\Payment\CreateCardCheckout;
use App\UseCases\Payment\CreatePixPayment;

class CheckoutController extends Controller
{
    public function __construct(
        protected PlaceOrder $placeOrder,
        protected CreatePixPayment $createPixPayment,
        protected CreateBoletoPayment $createBoletoPayment,
        protected CreateCardCheckout $createCardCheckout,
        protected PaymentResponsePresenter $presenter,
    ) {}

    public function processOrder(CheckoutRequest $request)
    {
        $order = $this->placeOrder->execute(
            $request->user(),
            $request->validated()
        );

        return match ($request->validated('payment_method')) {
            'pix' => $this->presenter->pix($order, $this->createPixPayment->execute($order)),
            'boleto' => $this->presenter->boleto($order, $this->createBoletoPayment->execute($order)),
            'card' => $this->cardCheckout($order),
        };
    }

    public function index(CheckoutViewService $service)
    {
        return view('public.checkout.index', $service->getData());
    }

    private function cardCheckout(Order $order)
    {
        try {
            return $this->presenter->card($this->createCardCheckout->execute($order));
        } catch (PaymentException) {
            return $this->presenter->cardError($order);
        }
    }
}
