<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompraRequest;
use App\Services\CompraService;
use Illuminate\Http\JsonResponse;
use Exception;

class CompraController extends Controller
{
    public function __construct(private CompraService $compras)
    {
    }

    public function index()
    {
        return response()->json($this->compras->listar());
    }

    public function store(CompraRequest $entrada): JsonResponse
    {
        try {
            $dadosTratados = $entrada->validated();
            $registro = $this->compras->registrar($dadosTratados);

            return response()->json([
                'compra' => $registro,
                'message' => 'Compra registrada com sucesso.'
            ], 201);
        } catch (Exception $e) {
            $codigo = $e->getCode() === 0 ? 500 : $e->getCode();

            return response()->json(['message' => 'Erro durante a compra'], $codigo);
        }
    }
}

