<?php

namespace App\Services;

use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Exception;

class VendaService
{
    public function listar()
    {
        return Venda::with('itens.produto')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function registrar(array $entrada)
    {
        $itensSelecionados = $entrada['produtos'] ?? [];

        try {
            DB::beginTransaction();

            $faltandoEstoque = [];
            foreach ($itensSelecionados as $linhaProduto) {
                $produto = Produto::find($linhaProduto['id'] ?? null);
                if (!$produto) {
                    continue;
                }

                if ($produto->estoque < (int) ($linhaProduto['quantidade'] ?? 0)) {
                    $faltandoEstoque[] = $produto->nome;
                }
            }

            if (!empty($faltandoEstoque)) {
                throw new Exception('Sem estoque para: ' . implode(', ', $faltandoEstoque), 409);
            }

            $venda = Venda::create([
                'cliente' => $entrada['cliente'] ?? 'Cliente',
                'total' => 0,
                'lucro' => 0,
                'cancelada' => false
            ]);

            $valorTotal = 0;
            $lucroTotal = 0;

            foreach ($itensSelecionados as $linhaProduto) {
                $produto = Produto::find($linhaProduto['id'] ?? null);
                if (!$produto) {
                    continue;
                }

                $quantidade = (int) ($linhaProduto['quantidade'] ?? 0);
                $valorUnitario = (float) ($linhaProduto['preco_unitario'] ?? $produto->preco_venda);
                $subtotalLinha = $quantidade * $valorUnitario;
                $valorTotal += $subtotalLinha;

                $valorCusto = $quantidade * $produto->custo_medio;
                $lucroTotal += ($subtotalLinha - $valorCusto);

                $venda->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $valorUnitario,
                    'subtotal' => $subtotalLinha
                ]);

                $produto->estoque -= $quantidade;
                $produto->save();
            }

            $venda->total = $valorTotal;
            $venda->lucro = $lucroTotal;
            $venda->save();

            DB::commit();

            return $venda->load('itens.produto');
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function cancelar(Venda $venda)
    {
        try {
            DB::beginTransaction();

            if ($venda->cancelada) {
                throw new Exception('Essa venda já tinha sido cancelada.', 409);
            }

            foreach ($venda->itens as $item) {
                $produto = $item->produto;
                $produto->estoque += $item->quantidade;
                $produto->save();
            }

            $venda->cancelada = true;
            $venda->save();

            DB::commit();

            return $venda;
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}
