<?php

namespace App\Http\Controllers;

use App\Models\MotivoVisita;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MotivoVisitaController extends Controller
{
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'nome' => 'required',
    ]);
    return $validator;
  }
  public function index() {
    return response()->json(MotivoVisita::get());
  }
  public function store(Request $request) {
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
      MotivoVisita::create($data);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()]
      ], 500);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }
  public function update(Request $request, $id) {
    $validator = $this->companyValidator($request);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }

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
    try {
      DB::beginTransaction();

      MotivoVisita::find($id)->delete();
      
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao remover'], 400);
    }
    return response()->json(['message' => 'Removido com sucesso!']);
  }
}
