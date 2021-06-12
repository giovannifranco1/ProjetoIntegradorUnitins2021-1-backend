<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use App\Models\Visita;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RelatorioTecnicoController extends Controller
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
        'tecnico' => 'required|integer',
        'start' => 'required|date',
        'end' => 'required|date'
      ]);

      if ($validator->fails()) return response()->json([
        'message' => 'error',
        'errors' => $validator->errors()
      ]);

      $idTecnico = $request->tecnico;
      $start = new DateTime($request->start);
      $end = new DateTime($request->end);

      $tecnico = Tecnico::select(
        'tecnico.created_at as associado_em',
        'u.name as nome',
        'tecnico.sobrenome',
        'u.email',
        DB::raw('CONCAT(\'(\', t.codigo_area, \') \', t.numero) as phone')
      ) ->join('telefone as t', 't.id', 'tecnico.id_telefone')
        ->join('users as u', 'u.id', 'tecnico.id_user')
        ->find($idTecnico);

      $visitas = Visita::select(
        'v.dia_visita as diaVisita',
        'v.motivo_visita as motivos',
        'p.nome as propriedade',
        'v.status',
        'ps.nome as cooperado'
      ) ->from('visita as v')

        // JOIN's
        ->join('propriedade as p', 'p.id', 'v.id_propriedade')
        ->join('cooperado as c', 'c.id', 'p.id_cooperado')
        ->join('tecnico as t', 't.id', 'v.id_tecnico')
        ->join('pessoa as ps', 'ps.id', 'c.id_pessoa')

        // Condicionais
        ->where('t.id', $idTecnico)
        ->whereBetween('dia_visita', [$start, $end])
        ->get();

      return response()->json([
        'tecnico' => $tecnico,
        'periodo' => [
          'inicio' => $request->start,
          'fim' => $request->end
        ],
        'visitas' => $visitas
      ]);
    }
}
