<?php

namespace App\Http\Controllers;

use App\Models\Cooperado;
use App\Models\Pessoa;
use App\Models\Tecnico;
use App\Models\Telefone;
use App\Models\Propriedade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CooperadoController extends Controller {
  private function companyValidator($request){
    $validator = Validator::make($request->all(), [
      'nome' => 'required|max:255',
      'sobrenome' => 'required|max:255',
      'email' => 'required|email',
      'cpf' => 'required|max:14|min:14',
      'telefone' => 'required|array',
      'telefone.codigo_area' => 'string|min:2|max:2',
      'telefone.numero' => 'string|regex:/^[0-9]{1} [0-9]{4}-[0-9]{4}/i',
    ]);
    return $validator;
  }
  public function store(Request $request) {
    $validator = $this->companyValidator($request);
    if($validator->fails() ) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }
    try {
      DB::beginTransaction();

      $inputs = $request->all();
      $inputs['numero'] = $request->telefone['numero'];
      $inputs['codigo_area'] = $request->telefone['codigo_area'];
      $inputs['status'] = true;

      # cadastro telefone
      $telefone = Telefone::create($inputs);
      $inputs['id_telefone'] = $telefone->id;

      # cadastro pessoa
      $pessoa = Pessoa::create($inputs);

      # cadastro cooperado
      $inputs['id_pessoa'] = $pessoa->id;
      $cooperado= Cooperado::create($inputs);

      #cadastro de propriedades
      foreach ($inputs['propriedades'] as $key => $value) {
        $inputs['propriedades'][$key]['id_cooperado'] = $cooperado->id;

        Propriedade::create($inputs['propriedades'][$key]);
      }


      DB::commit();
    }catch (Exception $e) {
      DB::rollback();

      return response()->json([
        'status' => 'error',
        'errors' => $e->getMessages()
      ], 500);
    }
    return response()->json(['status' => 'success']);
  }
  public function findAll(){
    return Cooperado::select('cooperado.id','p.nome as nome_cooperado' , 'p.cpf as cpf_cooperado',)
      ->join('pessoa as p' ,'p.id', 'cooperado.id_pessoa')
      ->get();
  }
  public function findById($id) {
    $propriedades = Propriedade::where('id_cooperado', $id)->get();

    $cooperado = Cooperado::select(
      'cooperado.id',
      'cooperado.status',
      'p.nome',
      'p.sobrenome',
      'p.email',
      'p.cpf',
      DB::raw('CONCAT(\'(\', t.codigo_area, \') \', t.numero) as phone')
    )
    ->join('pessoa as p', 'p.id', 'cooperado.id_pessoa')
    ->join('telefone as t', 't.id', 'p.id_telefone')
    ->first();

    $cooperado['propriedades'] = $propriedades;

    return response()->json($cooperado);
  }
}
