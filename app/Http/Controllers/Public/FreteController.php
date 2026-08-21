<?php

namespace App\Http\Controllers\Public;

use App\Actions\Shipping\CalculateShippingOptionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Frete\FreteRequest;
use App\Models\Product;
use App\Services\OperationalAlertService;
use Illuminate\Support\Facades\Log;

class FreteController extends Controller
{
    public function __construct(private readonly OperationalAlertService $alerts) {}

    public function calcular(FreteRequest $request, CalculateShippingOptionsAction $calculateShipping)
    {
        try {
            $data = $request->validated();
            $product = isset($data['product_id']) ? Product::find($data['product_id']) : null;

            return response()->json($calculateShipping->execute($data['cep'], $product));
        } catch (\Throwable $e) {
            Log::error('Falha ao calcular opções de frete.', [
                'cep_prefix' => substr($request->validated()['cep'], 0, 5),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            $this->alerts->critical('Falha ao consultar opções de frete.', [
                'CEP (prefixo)' => substr($request->validated()['cep'], 0, 5),
                'Tipo de erro' => $e::class,
            ]);

            return response()->json([
                'erro' => true,
                'mensagem' => 'Não foi possível calcular o frete agora. Tente novamente em instantes.',
            ], 500);
        }
    }
}
