<?php

namespace App\Services;

use App\Models\Pessoa;

class PessoaService
{
    public function listar()
    {
        return Pessoa::orderBy('created_at', 'desc')->get();
    }

    public function buscar($id)
    {
        return Pessoa::findOrFail($id);
    }

    public function criar(array $dados)
    {
        return Pessoa::create($dados);
    }

    public function atualizar(Pessoa $pessoa, array $dados)
    {
        $pessoa->update($dados);
        return $pessoa->fresh();
    }

    public function excluir(Pessoa $pessoa)
    {
        $pessoa->delete();
    }

    public function estatisticas()
    {
        $total        = Pessoa::count();
        $totalFisicas  = Pessoa::where('tipo', 'fisica')->count();
        $totalJuridicas = Pessoa::where('tipo', 'juridica')->count();
        $hoje         = Pessoa::whereDate('created_at', today())->count();
        $estaSemana   = Pessoa::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();
        $esteMes      = Pessoa::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $ultimos7Dias = collect(range(6, 0))->map(function ($i) {
            $data  = now()->subDays($i);
            $count = Pessoa::whereDate('created_at', $data->toDateString())->count();
            return ['data' => $data->format('d/m'), 'count' => $count];
        })->values();

        $recentes = Pessoa::orderBy('created_at', 'desc')->take(5)->get();

        return compact(
            'total',
            'totalFisicas',
            'totalJuridicas',
            'hoje',
            'estaSemana',
            'esteMes',
            'ultimos7Dias',
            'recentes'
        );
    }
}
