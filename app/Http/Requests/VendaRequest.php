<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente' => ['required', 'string', 'min:2'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.id' => ['required', 'integer'],
            'produtos.*.quantidade' => ['required', 'integer', 'min:1'],
            'produtos.*.preco_unitario' => ['nullable', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'cliente.required' => 'Informe o nome do cliente.',
            'cliente.min' => 'Nome do cliente muito curto.',
            'produtos.required' => 'Inclua ao menos um produto na venda.',
            'produtos.array' => 'Produtos devem estar em formato de lista.',
            'produtos.*.id.required' => 'Selecione um produto válido.',
            'produtos.*.quantidade.required' => 'Informe a quantidade vendida.',
            'produtos.*.quantidade.min' => 'Quantidade vendida mínima é 1.',
            'produtos.*.preco_unitario.numeric' => 'Preço informado precisa ser numérico.'
        ];
    }
}
