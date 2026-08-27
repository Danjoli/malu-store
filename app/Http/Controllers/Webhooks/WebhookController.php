<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAsaasWebhook;
use App\Services\Admin\Shipments\MelhorEnvioWebhookService;
use App\Services\Security\WebhookSignatureValidator;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected MelhorEnvioWebhookService $melhorEnvioWebhookService,
        protected WebhookSignatureValidator $signatureValidator,
    ) {}

    public function melhorEnvio(Request $request)
    {
        if (! $this->signatureValidator->melhorEnvio(
            $request->getContent(),
            $request->header('X-ME-Signature'),
        )) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $this->melhorEnvioWebhookService->handleMelhorEnvio(
            $request->all()
        );

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function asaas(Request $request)
    {
        if (! $this->signatureValidator->asaas($request->header('asaas-access-token'))) {
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
