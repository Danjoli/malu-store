<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Payment\ProcessCardPaymentRequest;
use App\Services\Public\Payment\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /*
    |-------------------------
    | PIX
    |-------------------------
    */
    public function createPix($orderId)
    {
        return $this->paymentService->pix($orderId);
    }

    /*
    |-------------------------
    | BOLETO
    |-------------------------
    */
    public function createBoleto($orderId)
    {
        return $this->paymentService->boleto($orderId);
    }

    /*
    |-------------------------
    | CARTÃO
    |-------------------------
    */
    public function createCard($orderId)
    {
        return $this->paymentService->cardView($orderId);
    }

    public function processCard(ProcessCardPaymentRequest $request, $orderId)
    {
        return response()->json(
            $this->paymentService->processCard(
                $orderId,
                $request->validated()
            )
        );
    }

    /*
    |-------------------------
    | STATUS
    |-------------------------
    */
    public function status($orderId)
    {
        return response()->json(
            $this->paymentService->status($orderId)
        );
    }

    /*
    |-------------------------
    | RESULTADOS
    |-------------------------
    */
    // public function success($orderId)
    // {
    //     return $this->paymentService->success($orderId);
    // }

    // public function error($orderId, $reason)
    // {
    //     return $this->paymentService->error($orderId, $reason);
    // }
}
