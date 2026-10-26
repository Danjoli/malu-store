<?php

namespace App\Http\Presenters;

use App\Data\Payments\BoletoPaymentResult;
use App\Data\Payments\PixPaymentResult;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentResponsePresenter
{
    public function method(Order $order): View
    {
        return view('public.payments.index', compact('order'));
    }

    public function pix(Order $order, PixPaymentResult $result): View
    {
        return view('public.payments.methods.pix', [
            'order' => $order,
            'payment' => $result->payment,
            'qr_code_base64' => $result->qrCodeBase64,
            'qr_code' => $result->qrCode,
        ]);
    }

    public function boleto(Order $order, BoletoPaymentResult $result): View
    {
        return view('public.payments.methods.boleto', [
            'order' => $order,
            'payment' => $result->payment,
            'boleto_url' => $result->boletoUrl,
            'invoice_url' => $result->invoiceUrl,
            'expires_at' => $result->expiresAt,
        ]);
    }

    public function card(string $url): RedirectResponse
    {
        return redirect()->away($url);
    }

    public function cardError(Order $order): RedirectResponse
    {
        return redirect()->route('payment.error', $order->id)
            ->with('error', 'Não foi possível abrir o ambiente seguro de pagamento. Tente novamente.');
    }

    public function success(Order $order): View
    {
        return view('public.payments.result.success', compact('order'));
    }

    public function error(Order $order, string $reason): View
    {
        return view('public.payments.result.error', compact('order', 'reason'));
    }

    public function status(Order $order): JsonResponse
    {
        return response()->json([
            'status' => $order->status,
            'gateway_status' => $order->gateway_status,
        ]);
    }
}
