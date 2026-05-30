<?php

use App\Http\Controllers\PessoaController;
use Illuminate\Support\Facades\Route;

Route::get('/pessoas/statistics', [PessoaController::class, 'statistics']);
Route::apiResource('pessoas', PessoaController::class);
