<?php

use App\Http\Controllers\PessoaController;
use Illuminate\Support\Facades\Route;

Route::get('/pessoas/statistics', [PessoaController::class, 'statistics']);
Route::get('/pessoas', [PessoaController::class, 'index']);
Route::get('/pessoas/{id}', [PessoaController::class, 'show']);
Route::post('/pessoas', [PessoaController::class, 'store']);
Route::put('/pessoas/{id}', [PessoaController::class, 'update']);
Route::delete('/pessoas/{id}', [PessoaController::class, 'destroy']);
