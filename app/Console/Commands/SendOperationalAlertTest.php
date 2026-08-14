<?php

namespace App\Console\Commands;

use App\Services\OperationalAlertService;
use Illuminate\Console\Command;

class SendOperationalAlertTest extends Command
{
    protected $signature = 'alerts:test';

    protected $description = 'Envia um e-mail de teste para o destinatário configurado em ALERT_EMAIL';

    public function handle(OperationalAlertService $alerts): int
    {
        if (! config('alerts.email')) {
            $this->error('Defina ALERT_EMAIL no arquivo .env antes de testar.');

            return self::FAILURE;
        }

        $alerts->critical('Teste de canal de alerta da Malu Store.', [
            'Ambiente' => config('app.env'),
        ]);

        $this->info('Alerta de teste solicitado. Verifique a caixa de entrada e o log do Laravel.');

        return self::SUCCESS;
    }
}
