<?php

namespace Tests\Feature;

use App\Jobs\ProcessAsaasWebhook;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class WebhookQueueTest extends TestCase
{
    public function test_authorized_asaas_webhook_is_dispatched_to_queue(): void
    {
        Bus::fake();
        config(['services.asaas.webhook_token' => 'test-token']);

        $this->postJson('/api/webhooks/asaas', ['event' => 'PAYMENT_RECEIVED'], ['asaas-access-token' => 'test-token'])
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        Bus::assertDispatched(ProcessAsaasWebhook::class, fn (ProcessAsaasWebhook $job) => $job->payload['event'] === 'PAYMENT_RECEIVED');
    }

    public function test_unauthorized_asaas_webhook_is_rejected(): void
    {
        config(['services.asaas.webhook_token' => 'test-token']);
        $this->postJson('/api/webhooks/asaas', ['event' => 'PAYMENT_RECEIVED'])
            ->assertUnauthorized();
    }
}
