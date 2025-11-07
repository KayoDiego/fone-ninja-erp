<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'custo_medio',
        'preco_venda',
        'estoque'
    ];

    protected $casts = [
        'custo_medio' => 'decimal:2',
        'preco_venda' => 'decimal:2',
        'estoque' => 'integer'
    ];

    public function itensCompra(): HasMany
    {
        return $this->hasMany(ItemCompra::class);
    }

    public function itensVenda(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function atualizarCustoMedio(float $quantidade, float $precoUnitario): void
    {
        $estoqueAtual = $this->estoque;

        if ($estoqueAtual <= 0) {
            $this->custo_medio = $precoUnitario;
            $this->save();
            return;
        }

        $valorAtual = $estoqueAtual * $this->custo_medio;
        $valorNovo = $quantidade * $precoUnitario;
        $novoEstoque = $estoqueAtual + $quantidade;

        $this->custo_medio = ($valorAtual + $valorNovo) / $novoEstoque;
        $this->save();
    }
}