<?php

namespace App\Http\Controllers;

use App\Models\FotoTalhao;
use App\Models\Talhao;
use App\Models\Visita;

class HistoricoController extends Controller
{
  public function __construct()
  {
    $this->middleware('permission:gerenciar_visita');
  }

  public function findAll()
  {
    $visita = Visita::from('visita as v')
      ->select(
        'v.id',
        'u.name as nome_tecnico',
        'p.nome as nome_cooperado',
        'v.dia_visita',
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
      ->orderBy('v.dia_visita', 'desc')

    // Listagem
      ->get();
    // imagem visita
    $visita->talhoes = [];
    foreach ($visita as $i => $v) {
      $visita->talhoes[$i] = Talhao::from('talhao as t')
        ->select(
          'id',
          't.cultura',
          't.relatorio'
        )

      // Condições
        ->where('id_visita', $v->id)

      //Listagem
        ->get();

      foreach ($visita->talhoes[$i] as $x => $talhao) {
        $visita->talhoes[$i][$x]->fotos_visita = FotoTalhao::from('fotos_talhao as f')
          ->select('f.imagem as url_imagem')
          ->where('id_talhao', $talhao->id)
          ->get();
      }
    }
    $visitaResponse = $visita;
    $visitaResponse['talhoes'] = $visita->talhoes;

    return response()->json($visitaResponse);
  }
  public function findById($id)
  {
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

    $visita->talhoes = Talhao::where('id_visita', $id)->get();
    foreach ($visita->talhoes as $key => $talhao) {
      $visita->talhoes[$key]->foto_talhao = FotoTalhao::where('id_talhao', $talhao->id)->get();
    }
    return response()->json($visita);
  }
}
