<?php

namespace App\Http\Controllers\public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MelhorEnvioService;

class FreteController extends Controller
{
    public function calcular(Request $request, MelhorEnvioService $service)
    {
        $request->validate([
            'cep' => 'required|string'
        ]);

        $dados = [
            "from" => [
                "postal_code" => "01010-000" // seu CEP origem
            ],
            "to" => [
                "postal_code" => $request->cep
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

        // Filtra só opções válidas
        $fretes = collect($resultado)->filter(function ($item) {
            return isset($item['price']) && $item['price'] > 0;
        })->values();

        return response()->json($fretes);
    }
}
