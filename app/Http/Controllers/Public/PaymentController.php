<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Public\Payment\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Exibe a página de pagamento do pedido.
     */
    public function method(int $orderId)
    {
        return $this->paymentService->method($orderId);
    }

    /**
     * Processa o método de pagamento escolhido.
     */
    public function process(Request $request, int $orderId)
    {
        $request->validate([
            'payment_method' => [
                'required',
                'in:pix,card,boleto',
            ],
        ]);

        return match ($request->payment_method) {
            'pix' => $this->paymentService->pix($orderId),

            'card' => $this->paymentService->cardView($orderId),

            'boleto' => $this->paymentService->boleto($orderId),
        };
    }

    /**
     * Cria um pagamento via Pix.
     */
    public function pix(int $orderId)
    {
        return $this->paymentService->pix($orderId);
    }

    /**
     * Cria um pagamento via boleto.
     */
    public function boleto(int $orderId)
    {
        return $this->paymentService->boleto($orderId);
    }

    /**
     * Processa um pagamento via cartão.
     */
    public function card(Request $request, int $orderId)
    {
        return $this->paymentService->card($request, $orderId);
    }

    /**
     * Consulta status do pagamento.
     */
    public function status(int $orderId)
    {
        return $this->paymentService->status($orderId);
    }

    /**
     * Exibe a página de sucesso.
     */
    public function success(int $orderId)
    {
        return $this->paymentService->success($orderId);
    }

    /**
     * Exibe a página de erro.
     */
    public function error(int $orderId)
    {
        return $this->paymentService->error($orderId);
    }
}
