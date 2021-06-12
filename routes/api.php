<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
  return $request->user();
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function () {
  Route::post('login', 'App\Http\Controllers\Auth\AuthController@login')->name('login');
  Route::post('logout', 'App\Http\Controllers\Auth\AuthController@logout');
  Route::post('validate', 'App\Http\Controllers\Auth\AuthController@validateToken');
});
#Rotas Tecnico
Route::get('tecnico/index', 'App\Http\Controllers\TecnicoController@findAll');
Route::get('tecnico/data/{id}', 'App\Http\Controllers\TecnicoController@findById');
Route::post('tecnico/store', 'App\Http\Controllers\TecnicoController@store');
Route::put('tecnico/{id}', 'App\Http\Controllers\TecnicoController@update');
Route::put('tecnico/{id}/disable', 'App\Http\Controllers\TecnicoController@disable');
Route::put('tecnico/{id}/enable', 'App\Http\Controllers\TecnicoController@enable');
Route::put('tecnico/{id}/password', 'App\Http\Controllers\TecnicoController@changePassword');
Route::get('profile', 'App\Http\Controllers\TecnicoController@getProfile');
Route::put('profile', 'App\Http\Controllers\TecnicoController@editProfile');
Route::put('profile/password', 'App\Http\Controllers\TecnicoController@editProfilePassword');

#Rotas Cooperado
Route::get('cooperado/index', 'App\Http\Controllers\CooperadoController@findAll');
Route::get('cooperado/data/{id}', 'App\Http\Controllers\CooperadoController@findById');
Route::post('cooperado/store', 'App\Http\Controllers\CooperadoController@store');
Route::put('cooperado/{id}', 'App\Http\Controllers\CooperadoController@update');
Route::put('cooperado/{id}/disable', 'App\Http\Controllers\CooperadoController@disable');
Route::put('cooperado/{id}/enable', 'App\Http\Controllers\CooperadoController@enable');

#Propriedade
Route::get('propriedades/{cooperado}', 'App\Http\Controllers\PropriedadeController@findByCooperado');
Route::post('propriedade/store', 'App\Http\Controllers\PropriedadeController@create');
Route::put('propriedade/{id}', 'App\Http\Controllers\PropriedadeController@update');
Route::put('propriedade/transferir/{id}', 'App\Http\Controllers\PropriedadeController@transfer');

#Rotas painel
Route::get('painel', 'App\Http\Controllers\PainelController@index');

#Rotas Motivo visita
Route::get('motivos', 'App\Http\Controllers\MotivoVisitaController@index');
Route::post('motivos', 'App\Http\Controllers\MotivoVisitaController@store');
Route::put('motivos/{motivo}', 'App\Http\Controllers\MotivoVisitaController@update');
Route::delete('motivos/{motivo}', 'App\Http\Controllers\MotivoVisitaController@destroy');

#Rotas Visita
Route::get('visita/{visita}', 'App\Http\Controllers\VisitaController@findById');
Route::get('visitas/{tecnico}', 'App\Http\Controllers\VisitaController@findByTecnico');
Route::post('visitas', 'App\Http\Controllers\VisitaController@store');
Route::post('visitas/{visita}', 'App\Http\Controllers\VisitaController@update');
Route::delete('visitas/{visita}', 'App\Http\Controllers\VisitaController@destroy');
Route::put('visitas/image/{visita}', 'App\Http\Controllers\VisitaController@imageStore');

#Rotas Histórico
Route::get('historico', 'App\Http\Controllers\HistoricoController@findAll');
Route::get('historico/visita/{id}', 'App\Http\Controllers\HistoricoController@findById');

#Rotas Roles
Route::get('grupos', 'App\Http\Controllers\Auth\RoleController@index');
Route::post('grupos', 'App\Http\Controllers\Auth\RoleController@store');
Route::put('grupos/{visita}', 'App\Http\Controllers\Auth\RoleController@update');
Route::delete('grupos/{visita}', 'App\Http\Controllers\Auth\RoleController@destroy');

#Rota Email
