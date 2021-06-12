<?php

namespace App\Http\Controllers;

use App\Models\Cooperado;
use App\Models\Visita;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RelatorioCooperadoController extends Controller
{
  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function __invoke(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'cooperado' => 'required|integer',
      'start' => 'required|date',
      'end' => 'required|date',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'error',
        'errors' => $validator->errors(),
      ]);
    }

    $idCooperado = $request->cooperado;
    $start = new DateTime($request->start);
    $end = new DateTime($request->end);

    $cooperado = Cooperado::select(
      'cooperado.created_at as associado_em',
      'p.nome',
      'p.sobrenome',
      'p.email',
      DB::raw('CONCAT(\'(\', t.codigo_area, \') \', t.numero) as phone')
    )->join('pessoa as p', 'p.id', 'cooperado.id_pessoa')
      ->join('telefone as t', 't.id', 'p.id_telefone')
      ->find($idCooperado);

    $visitas = Visita::select(
      'v.dia_visita as diaVisita',
      'v.motivo_visita as motivos',
      'p.nome as propriedade',
      'v.status',
      't.nome as tecnico'
    )->from('visita as v')

    // JOIN's
      ->join('propriedade as p', 'p.id', 'v.id_propriedade')
      ->join('cooperado as c', 'c.id', 'p.id_cooperado')
      ->join('tecnico as t', 't.id', 'v.id_tecnico')

    // Condicionais
      ->where('c.id', $idCooperado)
      ->whereBetween('dia_visita', [$start, $end])
      ->get();

    return response()->json([
      'cooperado' => $cooperado,
      'periodo' => [
        'inicio' => $request->start,
        'fim' => $request->end,
      ],
      'visitas' => $visitas,
    ]);
  }
}
