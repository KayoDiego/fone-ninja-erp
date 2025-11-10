<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdutoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3'],
            'preco_venda' => ['required', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'Informe o nome do produto.',
            'nome.min' => 'Use pelo menos 3 letras.',
            'preco_venda.required' => 'Defina um preço sugerido.',
            'preco_venda.numeric' => 'Preço deve ser numérico.'
        ];
    }
}
