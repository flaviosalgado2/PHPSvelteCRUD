<?php

namespace App\Http\Controllers;

use App\Http\Requests\PessoaRequest;
use App\Http\Resources\PessoaResource;
use App\Services\PessoaService;

class PessoaController extends Controller
{
    public function __construct(private PessoaService $service) {}

    public function index()
    {
        return PessoaResource::collection($this->service->listar());
    }

    public function statistics()
    {
        $stats = $this->service->estatisticas();
        $stats['recentes'] = PessoaResource::collection($stats['recentes']);
        return response()->json($stats);
    }

    public function show($id)
    {
        return new PessoaResource($this->service->buscar($id));
    }

    public function store(PessoaRequest $request)
    {
        $pessoa = $this->service->criar($request->validated());
        return (new PessoaResource($pessoa))->response()->setStatusCode(201);
    }

    public function update(PessoaRequest $request, $id)
    {
        $pessoa = $this->service->buscar($id);
        return new PessoaResource($this->service->atualizar($pessoa, $request->validated()));
    }

    public function destroy($id)
    {
        $this->service->excluir($this->service->buscar($id));
        return response()->json(['message' => 'Pessoa excluída com sucesso']);
    }
}
