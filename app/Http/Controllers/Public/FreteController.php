<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Frete\FreteRequest;
use App\Services\Public\MelhorEnvio\MelhorEnvioService;

class FreteController extends Controller
{
    public function calcular(FreteRequest $request, MelhorEnvioService $service)
    {
        try {

            $dados = [
                "from" => [
                    "postal_code" => config('shipping.origin_zip')
                ],
                "to" => [
                    "postal_code" => $request->validated()['cep']
                ],
                "products" => [
                    [
                        "id" => "1",
                        "width" => 15,
                        "height" => 10,
                        "length" => 20,
                        "weight" => 1,
                        "insurance_value" => 100,
                        "quantity" => 1
                    ]
                ]
            ];

            $resultado = $service->calcularFrete($dados);

            $fretes = collect($resultado)
                ->filter(fn ($item) => isset($item['price']) && $item['price'] > 0)
                ->values();

            return response()->json($fretes);

        } catch (\Throwable $e) {

            return response()->json([
                'erro' => true,
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
            ], 500);

        }
    }
}
