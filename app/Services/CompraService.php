<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Exception;

class CompraService
{
    public function listar()
    {
        return Compra::with('itens.produto')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function registrar(array $payload)
    {
        $montanteTotal = 0;

        try {
            DB::beginTransaction();

            $compra = Compra::create([
                'fornecedor' => $payload['fornecedor'] ?? 'Fornecedor',
                'total' => 0
            ]);

            foreach ($payload['produtos'] as $registroProduto) {
                $produto = Produto::find($registroProduto['id'] ?? null);
                if (!$produto) {
                    continue;
                }

                $quantidade = (int) ($registroProduto['quantidade'] ?? 0);
                $valorUnitario = (float) ($registroProduto['preco_unitario'] ?? 0);
                $valorLinha = $quantidade * $valorUnitario;
                $montanteTotal += $valorLinha;

                $compra->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidade,
                'preco_unitario' => $valorUnitario,
                    'subtotal' => $valorLinha
                ]);

                $produto->atualizarCustoMedio($quantidade, $valorUnitario);
                $produto->estoque += $quantidade;
                $produto->save();
            }

            $compra->total = $montanteTotal;
            $compra->save();

            DB::commit();

            return $compra->load('itens.produto');
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
