<?php

namespace App\Http\Controllers;

use App\Models\Propriedade;
use App\Models\Visita;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelatorioPropriedadeController extends Controller
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
        'propriedade' => 'required|integer',
        'start' => 'required|date',
        'end' => 'required|date'
      ]);

      if ($validator->fails()) return response()->json([
        'message' => 'error',
        'errors' => $validator->errors()
      ]);

      $idPropriedade = $request->propriedade;
      $start = new DateTime($request->start);
      $end = new DateTime($request->end);

      $propriedade = Propriedade::select('created_at as cadastrada_em','nome') ->find($idPropriedade);

      $visitas = Visita::select(
        'v.dia_visita as diaVisita',
        'v.motivo_visita as motivos',
        'v.status',
        't.nome as tecnico'
      ) ->from('visita as v')

        // JOIN's
        ->join('propriedade as p', 'p.id', 'v.id_propriedade')
        ->join('cooperado as c', 'c.id', 'p.id_cooperado')
        ->join('tecnico as t', 't.id', 'v.id_tecnico')

        // Condicionais
        ->where('p.id', $idPropriedade)
        ->whereBetween('dia_visita', [$start, $end])
        ->get();

      return response()->json([
        'propriedade' => $propriedade,
        'periodo' => [
          'inicio' => $request->start,
          'fim' => $request->end
        ],
        'visitas' => $visitas
      ]);
    }
}
