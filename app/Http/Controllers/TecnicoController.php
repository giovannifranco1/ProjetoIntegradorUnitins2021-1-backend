<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Tecnico;
use App\Models\Telefone;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class TecnicoController extends Controller
{
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'nome' => 'required|max:255',
      'email' => 'required|email|unique:users',
      'senha' => 'required|min:8',
      'sobrenome' => 'required',
      'cpf' => 'required|max:14|min:14',
      'numero_registro' => 'required',
    ]);
    return $validator;
  }
  public function __construct()
  {
    //
  }
  private function rolesSync(Request $request, $tecnico)
  {
    $rolesRequest = $request->except(['_token', '_method']);
    foreach ($rolesRequest as $key => $value) {
      $roles[] = Role::where('id', $key)->first();
    }
    $tecnico = Tecnico::where('id', $tecnico)->first();
    if (!empty($roles)) {
      $tecnico->syncRoles($roles);
    } else {
      $tecnico->syncRoles(null);
    }
    return $tecnico->id;
  }
  public function store(Request $request)
  {
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validação inválida',
        'errors'  => $validator->errors()
      ], 422);
    }
    try {
      DB::beginTransaction();
      $inputs = $request->except(['id_grupo']);
      $inputs['numero'] = $request->telefone['numero'];
      $inputs['codigo_area'] = $request->telefone['codigo_area'];

      //cadastro telefone
      $telefone = Telefone::create($inputs);

      //cadastro user
      $inputs['name'] = $request->nome;
      $inputs['password'] = bcrypt($request->senha);
      $user = User::create($inputs);

      //cadastro tecnico
      $inputs = $request->except('id_grupo', 'telefone','password');
      $inputs['id_telefone'] = $telefone->id;
      $inputs['id_user'] = $user->id;
      $tecnico = Tecnico::create($inputs);
      DB::commit();
    }catch (Exception $e) {
      dd($e);
      DB::rollback();
      return response()->json(['message' => 'Erro ao cadastrar'], 500);
    }
    if ($telefone && $tecnico) {
      return response()->json(['message' => 'success']);
    }
  }
  public function update(Request $request, $id) {
    $validator = Validator::make($request->all(), [
      'nome' => 'required|max:255',
      'email' => 'required|email',
      'sobrenome' => 'required',
      'cpf' => 'required|max:14|min:14',
      'numero_registro' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validação inválida',
        'errors'  => $validator->errors()
      ], 422);
    }
    try {
      DB::beginTransaction();
      $inputs = $request->except(['id_grupo', 'id_tecnico']);

      $tecnico = Tecnico::find($id);
      $phone = Telefone::find($tecnico->id_telefone);
      $user = User::find($tecnico->id_user);

      $data = $request->all();
      $data['numero'] = $request->telefone['numero'];
      $data['codigo_area'] = $request->telefone['codigo_area'];

      $tecnico->update($data);
      $phone->update($data);
      $user->update($data);

      DB::commit();
    }catch (Exception $e) {
      dd($e);
      DB::rollback();
      return response()->json(['message' => 'Não foi possível alterar os dados.'], 500);
    }
    return response()->json(['message' => 'success']);
  }
  public function findAll() {
    return Tecnico::select(
      'tecnico.id',
      'tecnico.nome as nome_tecnico',
      'tecnico.cpf as cpf_tecnico',
    )->get();
  }
  public function findById($id) {
    return Tecnico::select(
      'tecnico.id',
      'tecnico.nome',
      'tecnico.sobrenome',
      'tecnico.cpf',
      'u.email',
      'tecnico.numero_registro',
      DB::raw('CONCAT(\'(\', t.codigo_area, \') \', t.numero) as phone')
    )
    ->join('users as u', 'u.id', 'tecnico.id_user')
    ->join('telefone as t', 't.id', 'tecnico.id_telefone')
    ->where('tecnico.id', $id)
    ->first();
  }
}
