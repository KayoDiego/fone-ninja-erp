<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\ProdutoService;
use Exception;

class ProdutoController extends Controller
{
    public function __construct(private ProdutoService $produtoService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->produtoService->listar());
    }

    public function store(Request $solicitacao): JsonResponse
    {
        $dadosValidados = $solicitacao->validate([
            'nome' => ['required', 'string', 'min:3'],
            'preco_venda' => ['required', 'numeric']
        ]);

        try {
            $novoProduto = $this->produtoService->criar($dadosValidados);

            return response()->json([
                'produto' => $novoProduto,
                'message' => 'Produto cadastrado com sucesso.'
            ], 201);
        } catch (Exception) {
            return response()->json(['message' => 'Erro interno'], 500);
        }
    }
}

