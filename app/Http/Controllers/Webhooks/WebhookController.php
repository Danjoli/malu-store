<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\Public\Payment\MercadoPagoWebhookService;
use Illuminate\Http\Request;

use App\Services\Admins\Shipment\MelhorEnvioWebhookService;

class WebhookController extends Controller
{
    public function __construct(
        protected MercadoPagoWebhookService $mercadoPagoWebhookService,
        protected MelhorEnvioWebhookService $melhorEnvioWebhookService
    ) {}

    public function mercadopago(Request $request)
    {
        $this->mercadoPagoWebhookService->handleMercadoPago($request);

        return response()->json(['status' => 'ok'], 200);
    }

    public function melhorEnvio(Request $request)
    {
        $this->melhorEnvioWebhookService->handleMelhorEnvio($request->all());

        return response()->json(['status' => 'ok']);
    }
}
