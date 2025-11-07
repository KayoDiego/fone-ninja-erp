<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendaRequest;
use App\Services\VendaService;
use App\Models\Venda;
use Illuminate\Http\JsonResponse;
use Exception;

class VendaController extends Controller
{
    public function __construct(private VendaService $vendas)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->vendas->listar());
    }

    public function store(VendaRequest $dados): JsonResponse
    {
        try {
            $conteudoValido = $dados->validated();
            $registro = $this->vendas->registrar($conteudoValido);

            return response()->json([
                'venda' => $registro,
                'total' => $registro->total,
                'lucro' => $registro->lucro,
                'message' => 'Venda salva.'
            ], 201);
        } catch (Exception $e) {
            $codigo = $e->getCode() === 0 ? 500 : $e->getCode();

            return response()->json(['message' => 'Erro ao salvar venda'], $codigo);
        }
    }

    public function cancelar(Venda $venda): JsonResponse
    {
        try {
            $cancelada = $this->vendas->cancelar($venda);

            return response()->json([
                'venda' => $cancelada,
                'message' => 'Estoque devolvido e venda cancelada.'
            ]);
        } catch (Exception $e) {
            $codigo = $e->getCode() === 0 ? 500 : $e->getCode();

            return response()->json(['message' => 'Erro ao cancelar venda'], $codigo);
        }
    }
}

