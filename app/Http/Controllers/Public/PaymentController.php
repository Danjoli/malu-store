<?php

namespace App\Http\Controllers\Public;

use App\Exceptions\Domain\PaymentException;
use App\Http\Controllers\Controller;
use App\Http\Presenters\PaymentResponsePresenter;
use App\Models\Order;
use App\UseCases\Payment\CreateBoletoPayment;
use App\UseCases\Payment\CreateCardCheckout;
use App\UseCases\Payment\CreatePixPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private CreatePixPayment $createPixPayment,
        private CreateBoletoPayment $createBoletoPayment,
        private CreateCardCheckout $createCardCheckout,
        private PaymentResponsePresenter $presenter,
    ) {}

    public function method(Request $request, int $orderId)
    {
        return $this->presenter->method($this->ownedOrder($request, $orderId));
    }

    public function process(Request $request, int $orderId)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:pix,card,boleto'],
        ]);

        return match ($validated['payment_method']) {
            'pix' => $this->pix($request, $orderId),
            'card' => $this->card($request, $orderId),
            'boleto' => $this->boleto($request, $orderId),
        };
    }

    public function pix(Request $request, int $orderId)
    {
        $order = $this->ownedOrder($request, $orderId);

        return $this->presenter->pix($order, $this->createPixPayment->execute($order));
    }

    public function boleto(Request $request, int $orderId)
    {
        $order = $this->ownedOrder($request, $orderId);

        return $this->presenter->boleto($order, $this->createBoletoPayment->execute($order));
    }

    public function card(Request $request, int $orderId)
    {
        $order = $this->ownedOrder($request, $orderId);

        try {
            return $this->presenter->card($this->createCardCheckout->execute($order));
        } catch (PaymentException) {
            return $this->presenter->cardError($order);
        }
    }

    public function status(Request $request, int $orderId)
    {
        return $this->presenter->status($this->ownedOrder($request, $orderId));
    }

    public function success(Request $request, int $orderId)
    {
        return $this->presenter->success($this->ownedOrder($request, $orderId));
    }

    public function error(Request $request, int $orderId)
    {
        return $this->presenter->error(
            $this->ownedOrder($request, $orderId),
            $request->string('reason')->toString(),
        );
    }

    private function ownedOrder(Request $request, int $orderId): Order
    {
        return Order::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($orderId);
    }
}
