<?php

namespace App\Http\Controllers;

use App\Models\Propriedade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropriedadeController extends Controller {
  private function companyValidator($request){
    $validator = Validator::make($request->all(), [
      'nome' => 'required|string|max:255',
      'tamanho_area' => 'required|numeric|min:0.01',
      'localidade' => 'required',
      'matricula' => 'required',
      'id_tecnico' => 'required',
      'id_cooperado' => 'required'
    ]);
    return $validator;
  }
  public function create(Request $request) {
    $validator = $this->companyValidator($request);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }

    $propriedade = $request->all();

    try {
      Propriedade::create($propriedade);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()]
      ], 500);
    }
    return response()->json(['message' => 'success']);
  }
  public function update(Request $request, $id) {
    $validator = $this->companyValidator($request);

    if($validator->fails() ) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }
    try {
      $data = $request->all();

      Propriedade::find($id)->update($data);
    } catch (Exception $e) {
      response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }
  public function transfer(Request $request, $id) {
    $validator = Validator::make(
      $request->all(),
      ['cooperado' => 'required|integer']
    );

    if ($validator->fails()) {
      return response()->json([
        'message' => 'fail',
        'errors' => $validator->errors()
      ]);
    }

    $cooperado = $request->cooperado;

    try {
      Propriedade::find($id)->update(['id_cooperado' => $cooperado]);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()]
      ], 500);
    }
    return response()->json(['message' => 'success']);
  }
  public function findByCooperado($cooperado) {
    return Propriedade::where('id_cooperado', $cooperado)
      ->select('id', 'nome', 'localidade', 'tamanho_area', 'matricula', 'id_cooperado', 'id_tecnico')
      ->get();
  }
}
