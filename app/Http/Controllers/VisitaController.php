<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use App\Models\Visita;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitaController extends Controller
{
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'horaEstimada' => 'required|date',
      'dia_visita' => 'required|date',
      'id_user' => 'required',
      'id_propriedade' => 'required',
      'motivo_visita' => 'required|string'
    ]);
    return $validator;
  }

  private function validateUpdate($request) {
    $validator = Validator::make($request->all(), [
      'horaEstimada' => 'required|date',
      'dia_visita' => 'required|date',
      'motivo_visita' => 'required|string',
    ]);
    return $validator;
  }

  public function findById($id)
  {
    $visita = Visita::select('visita.*', 'p.nome as cooperado', 'pr.nome as propriedade')
      ->join('propriedade as pr', 'pr.id', 'visita.id_propriedade')
      ->join('cooperado as c', 'c.id', 'pr.id_cooperado')
      ->join('pessoa as p', 'p.id', 'c.id_pessoa')
      ->where('visita.id', $id)
      ->first();

    return response()->json($visita);
  }

  public function findByTecnico($id) {
    $visitas = Visita::select('visita.*', 'p.nome as propriedade')
      ->join('propriedade as p', 'p.id', 'visita.id_propriedade')
      ->join('tecnico as t', 't.id', 'visita.id_tecnico')
      ->join('users as u', 'u.id', 't.id_user')
      ->where('u.id', $id)
      ->get();

    return response()->json($visitas);
  }

  public function store(Request $request)
  {
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }

    $data = $request->all();
    $data['dia_visita'] = new DateTime($data['dia_visita']);
    $data['horario_estimado_visita'] = new DateTime($data['horaEstimada']);

    try {
      DB::beginTransaction();
      $tecnico = Tecnico::select('id')
        ->where('id_user', $request->id_user)
        ->first();

      $data['id_tecnico'] = $tecnico->id;

      Visita::create($data);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()]
      ], 400);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }

  public function update(Request $request, $id)
  {
    $validator = $this->validateUpdate($request);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }
    $data = $request->all();
    $visita = Visita::find($id);
    try {
      DB::beginTransaction();
      $visita->update($data);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()]
      ], 400);
    }
    return response()->json(['message' => 'Editado com sucesso!']);
  }

  public function destroy(Request $request, $id)
  {
    $data = $request->all();
    $visita = Visita::find($id);
    try {
      DB::beginTransaction();
      $visita->delete();
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao remover'], 400);
    }
    return response()->json(['message' => 'Removido com sucesso!']);
  }
}
