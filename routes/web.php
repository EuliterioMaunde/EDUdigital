<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Rota resource responsável por mostrar a tela com o calendário e registar os dados no ficheiro de testes
Route::get("/", [\App\Http\Controllers\CalendarController::class, "index"])->name("home");
Route::resource('/calendar', \App\Http\Controllers\CalendarController::class, ['names' => 'calendar']);

