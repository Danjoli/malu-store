<?php

namespace Tests\Feature;

use App\Exceptions\Domain\PaymentGatewayException;
use App\Exceptions\Domain\ShippingProviderException;
use App\Services\Public\Payment\AsaasService;
use App\Services\Shipping\MelhorEnvioService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalProviderContractTest extends TestCase
{
    public function test_asaas_contract_uses_a_central_fixture_without_real_credentials(): void
    {
        Http::fake(['*' => Http::response($this->fixture('asaas/payment.json'))]);

        $payment = app(AsaasService::class)->getPayment('pay_fixture_1');

        $this->assertSame('pay_fixture_1', $payment['id']);
        $this->assertSame('PENDING', $payment['status']);
        Http::assertSentCount(1);
    }

    public function test_asaas_validation_error_is_classified_as_non_operational(): void
    {
        Http::fake(['*' => Http::response(['errors' => [['description' => 'invalid']]], 422)]);

        try {
            app(AsaasService::class)->getPayment('invalid');
            $this->fail('A resposta 422 deveria gerar uma exceção de domínio.');
        } catch (PaymentGatewayException $exception) {
            $this->assertFalse($exception->isOperational);
        }
    }

    public function test_asaas_timeout_is_classified_as_operational(): void
    {
        Http::fake(fn () => throw new ConnectionException('timeout'));

        try {
            app(AsaasService::class)->getPayment('pay_timeout');
            $this->fail('O timeout deveria gerar uma exceção de domínio.');
        } catch (PaymentGatewayException $exception) {
            $this->assertTrue($exception->isOperational);
        }
    }

    public function test_asaas_rejects_invalid_json(): void
    {
        Http::fake(['*' => Http::response('not-json', 200, ['Content-Type' => 'text/plain'])]);

        $this->expectException(PaymentGatewayException::class);
        app(AsaasService::class)->getPayment('pay_invalid_json');
    }

    public function test_melhor_envio_contract_uses_a_central_fixture(): void
    {
        Http::fake(['*' => Http::response($this->fixture('melhor-envio/calculation.json'))]);

        $options = app(MelhorEnvioService::class)->calcularFrete(['from' => ['postal_code' => '01001000']]);

        $this->assertSame('PAC', $options[0]['name']);
        Http::assertSentCount(1);
    }

    public function test_melhor_envio_handles_timeout_and_invalid_json(): void
    {
        Http::fake(fn () => throw new ConnectionException('timeout'));

        try {
            app(MelhorEnvioService::class)->calcularFrete([]);
            $this->fail('O timeout deveria gerar uma exceção de domínio.');
        } catch (ShippingProviderException) {
            $this->assertTrue(true);
        }

        Http::fake(['*' => Http::response('not-json', 200, ['Content-Type' => 'text/plain'])]);

        $this->expectException(ShippingProviderException::class);
        app(MelhorEnvioService::class)->calcularFrete([]);
    }

    private function fixture(string $path): array
    {
        $contents = file_get_contents(base_path('tests/Fixtures/'.$path));

        return json_decode($contents ?: '', true, flags: JSON_THROW_ON_ERROR);
    }
}
