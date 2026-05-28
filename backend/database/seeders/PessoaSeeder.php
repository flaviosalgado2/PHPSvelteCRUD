<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use Illuminate\Database\Seeder;

class PessoaSeeder extends Seeder
{
    public function run()
    {
        // Pessoas Físicas
        Pessoa::create([
            'tipo' => 'fisica',
            'nome' => 'João da Silva',
            'cpf' => '123.456.789-00',
            'data_nascimento' => '1985-03-15',
            'rg' => '12.345.678-9',
            'sexo' => 'M',
            'email' => 'joao.silva@email.com',
            'telefone' => '(11) 3456-7890',
            'celular' => '(11) 98765-4321',
            'cep' => '01310-100',
            'logradouro' => 'Av. Paulista',
            'numero' => '1000',
            'complemento' => 'Apto 101',
            'bairro' => 'Bela Vista',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        Pessoa::create([
            'tipo' => 'fisica',
            'nome' => 'Maria Santos',
            'cpf' => '987.654.321-00',
            'data_nascimento' => '1990-07-22',
            'rg' => '98.765.432-1',
            'sexo' => 'F',
            'email' => 'maria.santos@email.com',
            'celular' => '(21) 99876-5432',
            'cep' => '20040-020',
            'logradouro' => 'Av. Rio Branco',
            'numero' => '156',
            'bairro' => 'Centro',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
        ]);

        Pessoa::create([
            'tipo' => 'fisica',
            'nome' => 'Carlos Oliveira',
            'cpf' => '456.789.123-00',
            'data_nascimento' => '1978-11-30',
            'rg' => '45.678.912-3',
            'sexo' => 'M',
            'email' => 'carlos.oliveira@email.com',
            'telefone' => '(31) 3321-4567',
            'celular' => '(31) 98123-4567',
            'cep' => '30130-010',
            'logradouro' => 'Av. Afonso Pena',
            'numero' => '500',
            'bairro' => 'Centro',
            'cidade' => 'Belo Horizonte',
            'estado' => 'MG',
        ]);

        // Pessoas Jurídicas
        Pessoa::create([
            'tipo' => 'juridica',
            'razao_social' => 'Tech Solutions Ltda',
            'nome_fantasia' => 'TechSol',
            'cnpj' => '12.345.678/0001-90',
            'inscricao_estadual' => '123.456.789.012',
            'inscricao_municipal' => '123456',
            'email' => 'contato@techsol.com.br',
            'telefone' => '(11) 3000-1000',
            'celular' => '(11) 99000-1000',
            'cep' => '04543-011',
            'logradouro' => 'Av. Brigadeiro Faria Lima',
            'numero' => '2000',
            'complemento' => 'Sala 1001',
            'bairro' => 'Itaim Bibi',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);

        Pessoa::create([
            'tipo' => 'juridica',
            'razao_social' => 'Comércio Brasil EIRELI',
            'nome_fantasia' => 'Brasil Com',
            'cnpj' => '98.765.432/0001-10',
            'inscricao_estadual' => '987.654.321.098',
            'email' => 'vendas@brasilcom.com.br',
            'telefone' => '(21) 2500-3000',
            'cep' => '22250-040',
            'logradouro' => 'Rua Visconde de Pirajá',
            'numero' => '550',
            'bairro' => 'Ipanema',
            'cidade' => 'Rio de Janeiro',
            'estado' => 'RJ',
        ]);

        Pessoa::create([
            'tipo' => 'juridica',
            'razao_social' => 'Industria e Comércio ABC S.A.',
            'nome_fantasia' => 'ABC Industrial',
            'cnpj' => '11.222.333/0001-44',
            'inscricao_estadual' => '111.222.333.444',
            'inscricao_municipal' => '654321',
            'email' => 'contato@abcindustrial.com.br',
            'telefone' => '(41) 3200-4000',
            'celular' => '(41) 99200-4000',
            'cep' => '80060-000',
            'logradouro' => 'Rua XV de Novembro',
            'numero' => '1500',
            'bairro' => 'Centro',
            'cidade' => 'Curitiba',
            'estado' => 'PR',
        ]);
    }
}
