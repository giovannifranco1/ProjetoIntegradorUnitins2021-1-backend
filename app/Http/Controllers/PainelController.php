<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\Request;

class PainelController extends Controller
{
  public function index(Request $request)
  {
    $visita = Visita::from('visita as v')
      ->select(
        'v.*',
        'u.id as id_user',
        'u.name as nome_tecnico',
        'p.nome as nome_cooperado',
        'pr.nome as nome_propriedade',
      )
    // JOIN's
      ->join('propriedade as pr', 'pr.id', 'v.id_propriedade')
      ->join('cooperado as c', 'c.id', 'pr.id_cooperado')
      ->join('tecnico as t', 't.id', 'v.id_tecnico')
      ->join('pessoa as p', 'p.id', 'c.id_pessoa')
      ->join('users as u', 'u.id', 't.id_user')

    // Condições
      ->where('t.nome', 'like', "%{$request->nome_tecnico}%")
      ->where('p.nome', 'like', "%{$request->nome_cooperado}%")
      ->where('pr.nome', 'like', "%{$request->nome_propriedade}%")
      ->where('v.dia_visita', 'like', "%{$request->dia_visita}%")
      ->where('v.motivo_visita', 'like', "%{$request->motivo_visita}%")

    // Ordenação
      ->orderBy('v.dia_visita', 'desc')

    // Listagem
      ->get();

    return response()->json($visita);
  }
}
