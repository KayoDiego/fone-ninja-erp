<?php

namespace App\Services;

use App\Models\Produto;

class ProdutoService
{
    public function listar()
    {
        return Produto::select('id', 'nome', 'custo_medio', 'preco_venda', 'estoque')
            ->orderBy('nome')
            ->get();
    }

    public function criar(array $payload)
    {
        return Produto::create([
            'nome' => $payload['nome'] ?? '',
            'preco_venda' => $payload['preco_venda'] ?? 0,
            'custo_medio' => 0,
            'estoque' => 0
        ]);
    }
}
