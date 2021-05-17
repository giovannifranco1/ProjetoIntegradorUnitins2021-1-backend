<?php

namespace App\Http\Controllers;

use App\Models\MotivoVisita;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotivoVisitaController extends Controller
{
  public function index()
  {
    $motivos_visita = MotivoVisita::get();
    return response()->json(compact('motivos_visita'));
  }
  public function store(Request $request)
  {
    $data = $request->all();
    try {
      DB::beginTransaction();
      MotivoVisita::create($data);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      dd($e);
      return response()->json(['message' => 'Erro ao cadastrar'], 400);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }
  public function update(Request $request, $id)
  {
    $data = $request->all();
    $motivos_visita = MotivoVisita::find($id);
    try {
      DB::beginTransaction();
      $motivos_visita->update($data);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao editar'], 400);
    }
    return response()->json(['message' => 'Editado com sucesso!']);
  }
  public function destroy($id)
  {
    $motivos_visita = MotivoVisita::find($id);
    try {
      DB::beginTransaction();
      $motivos_visita->delete();
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao remover'], 400);
    }
    return response()->json(['message' => 'Removido com sucesso!']);
  }
}
