<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAsaasWebhookJob;
use App\Jobs\ProcessMelhorEnvioWebhookJob;
use App\Services\Security\WebhookSignatureValidator;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
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

        ProcessMelhorEnvioWebhookJob::dispatch($request->all());

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

        ProcessAsaasWebhookJob::dispatch($request->all());

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
