<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use Illuminate\Http\Request;

class PessoaController extends Controller
{
    public function index()
    {
        $pessoas = Pessoa::orderBy('created_at', 'desc')->get();
        return response()->json($pessoas);
    }

    public function statistics()
    {
        $total = Pessoa::count();
        $totalFisicas = Pessoa::where('tipo', 'fisica')->count();
        $totalJuridicas = Pessoa::where('tipo', 'juridica')->count();
        
        // Cadastros hoje
        $hoje = Pessoa::whereDate('created_at', today())->count();
        
        // Cadastros esta semana
        $inicioDaSemana = now()->startOfWeek();
        $fimDaSemana = now()->endOfWeek();
        $estaSemana = Pessoa::whereBetween('created_at', [$inicioDaSemana, $fimDaSemana])->count();
        
        // Cadastros este mês
        $esteMes = Pessoa::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Últimos 7 dias (para gráfico)
        $ultimos7Dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = now()->subDays($i);
            $count = Pessoa::whereDate('created_at', $data->toDateString())->count();
            $ultimos7Dias[] = [
                'data' => $data->format('d/m'),
                'count' => $count
            ];
        }
        
        // Cadastros recentes (últimos 5)
        $recentes = Pessoa::orderBy('created_at', 'desc')->take(5)->get();
        
        return response()->json([
            'total' => $total,
            'totalFisicas' => $totalFisicas,
            'totalJuridicas' => $totalJuridicas,
            'hoje' => $hoje,
            'estaSemana' => $estaSemana,
            'esteMes' => $esteMes,
            'ultimos7Dias' => $ultimos7Dias,
            'recentes' => $recentes
        ]);
    }

    public function show($id)
    {
        $pessoa = Pessoa::find($id);
        
        if (!$pessoa) {
            return response()->json(['message' => 'Pessoa não encontrada'], 404);
        }
        
        return response()->json($pessoa);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|in:fisica,juridica',
            'cpf' => 'nullable|string',
            'nome' => 'nullable|string',
            'data_nascimento' => 'nullable|date',
            'rg' => 'nullable|string',
            'sexo' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'razao_social' => 'nullable|string',
            'nome_fantasia' => 'nullable|string',
            'inscricao_estadual' => 'nullable|string',
            'inscricao_municipal' => 'nullable|string',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string',
            'celular' => 'nullable|string',
            'cep' => 'nullable|string',
            'logradouro' => 'nullable|string',
            'numero' => 'nullable|string',
            'complemento' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string',
        ]);

        $pessoa = Pessoa::create($data);
        
        return response()->json($pessoa, 201);
    }

    public function update(Request $request, $id)
    {
        $pessoa = Pessoa::find($id);
        
        if (!$pessoa) {
            return response()->json(['message' => 'Pessoa não encontrada'], 404);
        }

        $data = $request->validate([
            'tipo' => 'required|in:fisica,juridica',
            'cpf' => 'nullable|string',
            'nome' => 'nullable|string',
            'data_nascimento' => 'nullable|date',
            'rg' => 'nullable|string',
            'sexo' => 'nullable|string',
            'cnpj' => 'nullable|string',
            'razao_social' => 'nullable|string',
            'nome_fantasia' => 'nullable|string',
            'inscricao_estadual' => 'nullable|string',
            'inscricao_municipal' => 'nullable|string',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string',
            'celular' => 'nullable|string',
            'cep' => 'nullable|string',
            'logradouro' => 'nullable|string',
            'numero' => 'nullable|string',
            'complemento' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
            'estado' => 'nullable|string',
        ]);

        $pessoa->update($data);
        
        return response()->json($pessoa);
    }

    public function destroy($id)
    {
        $pessoa = Pessoa::find($id);
        
        if (!$pessoa) {
            return response()->json(['message' => 'Pessoa não encontrada'], 404);
        }
        
        $pessoa->delete();
        
        return response()->json(['message' => 'Pessoa excluída com sucesso']);
    }
}
