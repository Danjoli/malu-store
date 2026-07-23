<?php

namespace App\Services\Public\Payment;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsaasWebhookService
{
    /**
     * Processa o webhook recebido do Asaas.
     */
    public function handleAsaas(array $data): void
    {
        Log::info('Webhook Asaas recebido', [
            'event' => $data['event'] ?? null,
            'payment_id' => $data['payment']['id'] ?? null,
            'external_reference' => $data['payment']['externalReference'] ?? null,
        ]);

        $event = $data['event'] ?? null;

        if (!$event) {
            Log::warning('Webhook Asaas sem evento.');

            return;
        }

        match ($event) {
            'PAYMENT_CREATED' => $this->paymentCreated($data),

            'PAYMENT_CONFIRMED' => $this->paymentConfirmed($data),

            'PAYMENT_RECEIVED' => $this->paymentReceived($data),

            'PAYMENT_OVERDUE' => $this->paymentOverdue($data),

            'PAYMENT_DELETED' => $this->paymentDeleted($data),

            'PAYMENT_REFUNDED' => $this->paymentRefunded($data),

            default => Log::info(
                'Evento Asaas não tratado.',
                [
                    'event' => $event,
                ]
            ),
        };
    }

    /**
     * Pagamento criado.
     */
    protected function paymentCreated(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'pending',
            'PENDING'
        );
    }

    /**
     * Pagamento confirmado.
     */
    protected function paymentConfirmed(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'paid',
            'CONFIRMED'
        );
    }

    /**
     * Pagamento recebido.
     */
    protected function paymentReceived(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'paid',
            'RECEIVED'
        );
    }

    /**
     * Pagamento vencido.
     */
    protected function paymentOverdue(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'expired',
            'OVERDUE'
        );
    }

    /**
     * Pagamento excluído/cancelado.
     */
    protected function paymentDeleted(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'cancelled',
            'DELETED'
        );
    }

    /**
     * Pagamento estornado.
     */
    protected function paymentRefunded(array $data): void
    {
        $this->updateOrderStatus(
            $data,
            'cancelled',
            'REFUNDED'
        );
    }

    /**
     * Atualiza o pedido relacionado ao pagamento.
     */
    protected function updateOrderStatus(
        array $data,
        string $orderStatus,
        string $gatewayStatus
    ): void {
        $payment = $data['payment'] ?? [];

        $paymentId = $payment['id'] ?? null;

        $externalReference = $payment['externalReference'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Método de pagamento
        |--------------------------------------------------------------------------
        */

        $billingType = $payment['billingType'] ?? null;

        $paymentMethod = match ($billingType) {
            'PIX' => 'pix',

            'BOLETO' => 'boleto',

            'CREDIT_CARD' => 'card',

            default => $billingType
                ? strtolower($billingType)
                : null,
        };

        /*
        |--------------------------------------------------------------------------
        | Verifica se existe identificação do pagamento
        |--------------------------------------------------------------------------
        */

        if (!$paymentId && !$externalReference) {
            Log::warning(
                'Webhook Asaas sem payment ID ou externalReference.',
                $data
            );

            return;
        }

        $order = null;

        /*
        |--------------------------------------------------------------------------
        | Busca o pedido pelo ID do pagamento no Asaas
        |--------------------------------------------------------------------------
        */

        if ($paymentId) {
            $order = Order::where(
                'gateway_payment_id',
                $paymentId
            )->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Caso não encontre, busca pelo ID do pedido
        |--------------------------------------------------------------------------
        */

        if (!$order && $externalReference) {
            $order = Order::find($externalReference);
        }

        /*
        |--------------------------------------------------------------------------
        | Pedido não encontrado
        |--------------------------------------------------------------------------
        */

        if (!$order) {
            Log::warning(
                'Pedido não encontrado para webhook Asaas.',
                [
                    'payment_id' => $paymentId,
                    'external_reference' => $externalReference,
                ]
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Verifica se o pedido já estava pago
        |--------------------------------------------------------------------------
        |
        | Isso impede que o estoque seja baixado duas vezes caso o Asaas
        | envie PAYMENT_CONFIRMED e posteriormente PAYMENT_RECEIVED.
        |
        */

        $wasAlreadyPaid = $order->status === 'paid';

        /*
        |--------------------------------------------------------------------------
        | Dados para atualização
        |--------------------------------------------------------------------------
        */

        $updateData = [
            'status' => $orderStatus,

            'gateway_status' => $gatewayStatus,

            'gateway_payment_id' => $paymentId
                ?? $order->gateway_payment_id,

            'payment_method' => $paymentMethod
                ?? $order->payment_method,
        ];

        /*
        |--------------------------------------------------------------------------
        | Salva a data do pagamento
        |--------------------------------------------------------------------------
        */

        if (
            $orderStatus === 'paid'
            && !$order->paid_at
        ) {
            $updateData['paid_at'] = now();
        }

        /*
        |--------------------------------------------------------------------------
        | Atualiza o pedido
        |--------------------------------------------------------------------------
        */

        $order->update($updateData);

        /*
        |--------------------------------------------------------------------------
        | Finaliza o pedido após confirmação do pagamento
        |--------------------------------------------------------------------------
        |
        | Só executa na primeira vez que o pedido fica como "paid".
        |
        */

        if (
            $orderStatus === 'paid'
            && !$wasAlreadyPaid
        ) {
            $this->finalizePaidOrder($order);
        }

        /*
        |--------------------------------------------------------------------------
        | Log
        |--------------------------------------------------------------------------
        */

        Log::info(
            'Pedido atualizado via webhook Asaas.',
            [
                'order_id' => $order->id,

                'payment_id' => $paymentId,

                'payment_method' => $paymentMethod,

                'status' => $orderStatus,

                'gateway_status' => $gatewayStatus,

                'paid_at' => $updateData['paid_at'] ?? null,

                'was_already_paid' => $wasAlreadyPaid,
            ]
        );
    }

    /**
     * Finaliza o pedido após o pagamento.
     *
     * - Baixa o estoque dos produtos.
     * - Limpa o carrinho do usuário.
     */
    protected function finalizePaidOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {

            /*
            |--------------------------------------------------------------------------
            | Recarrega o pedido e seus itens
            |--------------------------------------------------------------------------
            */

            $order->load([
                'items.variant',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Baixa do estoque
            |--------------------------------------------------------------------------
            */

            foreach ($order->items as $item) {
                $variant = $item->variant;

                if (!$variant) {
                    Log::warning(
                        'Variante do produto não encontrada ao finalizar pedido.',
                        [
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'product_variant_id' => $item->product_variant_id,
                        ]
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Verifica estoque
                |--------------------------------------------------------------------------
                */

                if ($variant->stock < $item->quantity) {
                    Log::warning(
                        'Estoque insuficiente ao finalizar pedido.',
                        [
                            'order_id' => $order->id,
                            'product_variant_id' => $variant->id,
                            'stock_atual' => $variant->stock,
                            'quantidade_pedida' => $item->quantity,
                        ]
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Diminui estoque
                |--------------------------------------------------------------------------
                */

                $variant->decrement(
                    'stock',
                    $item->quantity
                );

                Log::info(
                    'Estoque atualizado após pagamento.',
                    [
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'quantidade_baixada' => $item->quantity,
                        'estoque_restante' => $variant->fresh()->stock,
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Limpa o carrinho do usuário
            |--------------------------------------------------------------------------
            */

            if ($order->user_id) {

                $cart = Cart::where(
                    'user_id',
                    $order->user_id
                )->first();

                if ($cart) {

                    $cart->items()->delete();

                    Log::info(
                        'Carrinho limpo após pagamento.',
                        [
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'cart_id' => $cart->id,
                        ]
                    );
                } else {

                    Log::info(
                        'Nenhum carrinho encontrado para limpar.',
                        [
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Log final
            |--------------------------------------------------------------------------
            */

            Log::info(
                'Pedido finalizado após pagamento.',
                [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ]
            );
        });
    }
}
