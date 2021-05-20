<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitaController extends Controller
{
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'horario_estimado_visita' => 'required|datetime',
      'dia_visita' => 'required|date',
      'id_tecnico' => 'required',
      'id_propriedade' => 'required',
      'motivo_visita' => 'required'
    ]);

    return $validator;
  }
  public function findById($id)
  {
    $visita = Visita::select('p_c.nome as nome_cooperado', 'propriedade.nome as nome_propriedade', 'visita.*')
      ->join('propriedade', 'propriedade.id', 'visita.id_propriedade')
      ->join('cooperado as c', 'c.id', 'propriedade.id_cooperado')
      ->join('pessoa as p_c', 'p_c.id', 'c.id_pessoa')
      ->where('p_c.id', "%{$id}%")
      ->orderBy('visita.dia_visita', 'desc')
      ->get();
    return response()->json($visita);
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
    try {
      DB::beginTransaction();
      Visita::create($data);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao cadastrar'], 400);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }

  public function update(Request $request, $id)
  {
    $validator = $this->companyValidator($request->all());
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
      return response()->json(['message' => 'Erro ao Editar'], 400);
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
