<?php

namespace Tests\Feature;

use App\Jobs\ProcessAsaasWebhookJob;
use App\Jobs\ProcessMelhorEnvioWebhookJob;
use App\Notifications\CriticalOperationalAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Tests\TestCase;

class WebhookQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_asaas_webhook_is_dispatched_to_queue(): void
    {
        Bus::fake();
        config(['services.asaas.webhook_token' => 'test-token']);

        $this->postJson('/api/webhooks/asaas', ['event' => 'PAYMENT_RECEIVED'], ['asaas-access-token' => 'test-token'])
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        Bus::assertDispatched(ProcessAsaasWebhookJob::class, fn (ProcessAsaasWebhookJob $job) => $job->payload['event'] === 'PAYMENT_RECEIVED'
            && $job->queue === 'critical');
    }

    public function test_unauthorized_asaas_webhook_is_rejected(): void
    {
        config(['services.asaas.webhook_token' => 'test-token']);
        $this->postJson('/api/webhooks/asaas', ['event' => 'PAYMENT_RECEIVED'])
            ->assertUnauthorized();
    }

    public function test_authorized_melhor_envio_webhook_is_accepted(): void
    {
        Bus::fake();
        config(['services.melhor_envio.webhook_secret' => 'webhook-secret']);

        $payload = [
            'id' => 'shipment_test_1',
            'status' => 'posted',
        ];
        $content = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = base64_encode(hash_hmac('sha256', $content, 'webhook-secret', true));

        $this->call(
            'POST',
            '/api/webhooks/melhor-envio',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_ME_SIGNATURE' => $signature,
            ],
            $content,
        )->assertOk()->assertJson(['status' => 'ok']);

        Bus::assertDispatched(
            ProcessMelhorEnvioWebhookJob::class,
            fn (ProcessMelhorEnvioWebhookJob $job): bool => $job->payload['id'] === 'shipment_test_1'
                && $job->queue === 'critical',
        );
    }

    public function test_melhor_envio_webhook_with_invalid_signature_is_rejected(): void
    {
        config(['services.melhor_envio.webhook_secret' => 'webhook-secret']);

        $this->postJson(
            '/api/webhooks/melhor-envio',
            ['id' => 'shipment_test_1', 'status' => 'posted'],
            ['X-ME-Signature' => 'invalid'],
        )->assertUnauthorized();
    }

    public function test_final_webhook_failure_sends_an_operational_alert(): void
    {
        Notification::fake();
        config(['alerts.email' => 'alerts@example.test']);

        $job = new ProcessAsaasWebhookJob([
            'event' => 'PAYMENT_RECEIVED',
            'payment' => ['id' => 'pay_test_1'],
        ]);

        $job->failed(new RuntimeException('Erro interno do gateway.'));

        Notification::assertSentOnDemand(
            CriticalOperationalAlert::class,
            fn (CriticalOperationalAlert $notification, array $channels): bool => $channels === ['mail'],
        );
    }
}
