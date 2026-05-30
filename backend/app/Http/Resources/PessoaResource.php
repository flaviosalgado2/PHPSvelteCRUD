<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PessoaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'tipo'                 => $this->tipo,
            'cpf'                  => $this->cpf,
            'nome'                 => $this->nome,
            'data_nascimento'      => $this->data_nascimento?->format('Y-m-d'),
            'rg'                   => $this->rg,
            'sexo'                 => $this->sexo,
            'cnpj'                 => $this->cnpj,
            'razao_social'         => $this->razao_social,
            'nome_fantasia'        => $this->nome_fantasia,
            'inscricao_estadual'   => $this->inscricao_estadual,
            'inscricao_municipal'  => $this->inscricao_municipal,
            'email'                => $this->email,
            'telefone'             => $this->telefone,
            'celular'              => $this->celular,
            'cep'                  => $this->cep,
            'logradouro'           => $this->logradouro,
            'numero'               => $this->numero,
            'complemento'          => $this->complemento,
            'bairro'               => $this->bairro,
            'cidade'               => $this->cidade,
            'estado'               => $this->estado,
            'nome_exibicao'        => $this->tipo === 'fisica' ? $this->nome : $this->razao_social,
            'documento_principal'  => $this->tipo === 'fisica' ? $this->cpf : $this->cnpj,
            'created_at'           => $this->created_at?->toISOString(),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
