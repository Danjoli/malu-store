<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAsaasWebhookJob;
use App\Jobs\ProcessMelhorEnvioWebhookJob;
use App\Services\Security\WebhookSignatureValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

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

        if (! $this->claim($request, 'melhor-envio')) {
            return response()->json(['status' => 'duplicate']);
        }

        try {
            ProcessMelhorEnvioWebhookJob::dispatch($request->all());
        } catch (Throwable $exception) {
            Cache::forget($this->fingerprint($request, 'melhor-envio'));

            throw $exception;
        }

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

        if (! $this->claim($request, 'asaas')) {
            return response()->json(['status' => 'duplicate']);
        }

        try {
            ProcessAsaasWebhookJob::dispatch($request->all());
        } catch (Throwable $exception) {
            Cache::forget($this->fingerprint($request, 'asaas'));

            throw $exception;
        }

        return response()->json([
            'status' => 'ok',
        ]);
    }

    private function claim(Request $request, string $provider): bool
    {
        return Cache::add(
            $this->fingerprint($request, $provider),
            true,
            now()->addDay(),
        );
    }

    private function fingerprint(Request $request, string $provider): string
    {
        return 'webhook:'.$provider.':'.hash('sha256', $request->getContent());
    }
}
