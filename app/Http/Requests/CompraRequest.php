<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fornecedor' => ['required', 'string', 'min:2'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.id' => ['required', 'integer'],
            'produtos.*.quantidade' => ['required', 'integer', 'min:1'],
            'produtos.*.preco_unitario' => ['required', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'fornecedor.required' => 'Informe o fornecedor.',
            'fornecedor.min' => 'Nome do fornecedor muito curto.',
            'produtos.required' => 'Adicione pelo menos um item na compra.',
            'produtos.array' => 'Produtos devem estar em uma lista.',
            'produtos.*.id.required' => 'Escolha um produto válido.',
            'produtos.*.quantidade.required' => 'Informe a quantidade comprada.',
            'produtos.*.quantidade.min' => 'Quantidade mínima é 1.',
            'produtos.*.preco_unitario.required' => 'Informe o preço unitário.',
            'produtos.*.preco_unitario.numeric' => 'Preço unitário inválido.'
        ];
    }
}
