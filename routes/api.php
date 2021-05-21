<?php

use App\Http\Controllers\Auth\RoleController as AuthRoleController;
use App\Http\Controllers\Auth\RoleController\RoleController;
use App\Models\MotivoVisita;
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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'App\Http\Controllers\Auth\AuthController@login')->name('login');
    Route::post('logout', 'App\Http\Controllers\Auth\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\Auth\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\Auth\AuthController@me');

});
#Rotas Tecnico
Route::get('tecnico/index', 'App\Http\Controllers\TecnicoController@findAll');
Route::get('tecnico/data/{id}', 'App\Http\Controllers\TecnicoController@findById');
Route::post('tecnico/store', 'App\Http\Controllers\TecnicoController@store');
Route::put('tecnico/{id}', 'App\Http\Controllers\TecnicoController@update');
Route::put('tecnico/{id}/disable', 'App\Http\Controllers\TecnicoController@disable');
Route::put('tecnico/{id}/enable', 'App\Http\Controllers\TecnicoController@enable');
// Route::put('tecnico/{id}/change_password', 'App\Http\Controllers\TecnicoController@changePassword');

#Rotas Cooperado
Route::post('cooperado/store', 'App\Http\Controllers\CooperadoController@store');
Route::get('cooperado/index', 'App\Http\Controllers\CooperadoController@findAll');
Route::get('cooperado/data/{id}', 'App\Http\Controllers\CooperadoController@findById');

#Rotas painel
Route::get('painel' , 'App\Http\Controllers\PainelController@index');

#Rotas Motivo visita
Route::get('motivos', 'App\Http\Controllers\MotivoVisitaController@index');
Route::post('motivos', 'App\Http\Controllers\MotivoVisitaController@store');
Route::put('motivos/{motivo}', 'App\Http\Controllers\MotivoVisitaController@update');
Route::delete('motivos/{motivo}', 'App\Http\Controllers\MotivoVisitaController@destroy');

#Rotas Visita
Route::get('visitas/{visita}', 'App\Http\Controllers\VisitaController@findById');
Route::post('visitas', 'App\Http\Controllers\VisitaController@store');
Route::put('visitas/{visita}', 'App\Http\Controllers\VisitaController@update');
Route::delete('visitas/{visita}', 'App\Http\Controllers\VisitaController@destroy');

#Rotas Roles
Route::get('grupos', 'App\Http\Controllers\Auth\RoleController@index');
Route::post('grupos', 'App\Http\Controllers\Auth\RoleController@store');
Route::put('grupos/{visita}', 'App\Http\Controllers\Auth\RoleController@update');
Route::delete('grupos/{visita}', 'App\Http\Controllers\Auth\RoleController@destroy');
