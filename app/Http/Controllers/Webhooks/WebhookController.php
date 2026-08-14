<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAsaasWebhook;
use App\Services\Admins\Shipment\MelhorEnvioWebhookService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected MelhorEnvioWebhookService $melhorEnvioWebhookService
    ) {}

    public function melhorEnvio(Request $request)
    {
        $this->melhorEnvioWebhookService->handleMelhorEnvio(
            $request->all()
        );

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function asaas(Request $request)
    {
        $token = $request->header('asaas-access-token');

        if ($token !== config('services.asaas.webhook_token')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        ProcessAsaasWebhook::dispatch($request->all());

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
