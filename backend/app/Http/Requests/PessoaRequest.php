<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PessoaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipo'                 => 'required|in:fisica,juridica',
            'cpf'                  => 'nullable|string|max:14',
            'nome'                 => 'nullable|string|max:255',
            'data_nascimento'      => 'nullable|date',
            'rg'                   => 'nullable|string|max:20',
            'sexo'                 => 'nullable|in:masculino,feminino,outro',
            'cnpj'                 => 'nullable|string|max:18',
            'razao_social'         => 'nullable|string|max:255',
            'nome_fantasia'        => 'nullable|string|max:255',
            'inscricao_estadual'   => 'nullable|string|max:20',
            'inscricao_municipal'  => 'nullable|string|max:20',
            'email'                => 'nullable|email|max:255',
            'telefone'             => 'nullable|string|max:20',
            'celular'              => 'nullable|string|max:20',
            'cep'                  => 'nullable|string|max:10',
            'logradouro'           => 'nullable|string|max:255',
            'numero'               => 'nullable|string|max:20',
            'complemento'          => 'nullable|string|max:100',
            'bairro'               => 'nullable|string|max:100',
            'cidade'               => 'nullable|string|max:100',
            'estado'               => 'nullable|string|max:2',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tipo = $this->input('tipo');

            if ($tipo === 'fisica' && empty($this->input('nome'))) {
                $validator->errors()->add('nome', 'O nome é obrigatório para pessoa física.');
            }

            if ($tipo === 'juridica' && empty($this->input('razao_social'))) {
                $validator->errors()->add('razao_social', 'A razão social é obrigatória para pessoa jurídica.');
            }
        });
    }

    public function messages()
    {
        return [
            'tipo.required'    => 'O tipo de pessoa é obrigatório.',
            'tipo.in'          => 'O tipo deve ser "fisica" ou "juridica".',
            'email.email'      => 'Informe um e-mail válido.',
            'data_nascimento.date' => 'Informe uma data de nascimento válida.',
        ];
    }
}
