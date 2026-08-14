<?php

namespace App\Http\Controllers\Public;

use App\Actions\Shipping\CalculateShippingOptionsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Frete\FreteRequest;
use Illuminate\Support\Facades\Log;

class FreteController extends Controller
{
    public function calcular(FreteRequest $request, CalculateShippingOptionsAction $calculateShipping)
    {
        try {
            return response()->json($calculateShipping->execute($request->validated()['cep']));
        } catch (\Throwable $e) {
            Log::error('Falha ao calcular opções de frete.', [
                'cep_prefix' => substr($request->validated()['cep'], 0, 5),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'erro' => true,
                'mensagem' => $e->getMessage(),
            ], 500);
        }
    }
}
