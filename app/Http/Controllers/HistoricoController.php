<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\Request;

class HistoricoController extends Controller
{
  public function __construct() {
    $this->middleware('permission:gerenciar_visita');
  }

  public function findAll() {
    $visita = Visita::from('visita as v')
      ->select(
        'v.id',
        'u.name as nome_tecnico',
        'p.nome as nome_cooperado',
        'v.dia_visita'
      )
      // JOIN's
      ->join('propriedade as pr', 'pr.id', 'v.id_propriedade')
      ->join('cooperado as c', 'c.id', 'pr.id_cooperado')
      ->join('tecnico as t', 't.id', 'v.id_tecnico')
      ->join('pessoa as p', 'p.id', 'c.id_pessoa')
      ->join('users as u', 'u.id', 't.id_user')

      // Condições
      ->where('v.status', 'concluido')
      ->orWhere('v.status', 'cancelado')

      // Ordenação
      ->orderBy('v.dia_visita' , 'desc')

      // Listagem
      ->get();

    return response()->json($visita);
  }
  public function findById($id) {
    $visita = Visita::from('visita as v')
      ->select(
        'v.*',
        'p.nome as cooperado',
        'pr.nome as propriedade'
      )
      ->join('propriedade as pr', 'pr.id', 'v.id_propriedade')
      ->join('cooperado as c', 'c.id', 'pr.id_cooperado')
      ->join('pessoa as p', 'p.id', 'c.id_pessoa')
      ->where('v.id', $id)
      ->first();

    return response()->json($visita);
  }
}
