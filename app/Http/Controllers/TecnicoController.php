<?php

namespace App\Http\Controllers;

use App\Models\Tecnico;
use App\Models\Telefone;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TecnicoController extends Controller
{

  public function __construct()
  {
    $this->middleware('permission:gerenciar_tecnico', ['except' => [
      'findAll', 'getProfile', 'editProfile', 'editProfilePassword',
    ]]);
  }

  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'nome' => 'required|max:255',
      'email' => 'required|email|unique:users',
      'senha' => 'required|min:8',
      'sobrenome' => 'required',
      'cpf' => 'required|max:14|min:14|unique:tecnico,cpf',
      'numero_registro' => 'required',
    ]);
    return $validator;
  }

  private function validateUpdate($request)
  {
    return Validator::make($request->all(), [
      'nome' => 'required|max:255',
      'email' => 'required|email',
      'sobrenome' => 'required',
      'cpf' => 'required|max:14|min:14',
      'numero_registro' => 'required',
    ]);
  }
  // private function rolesSync(Request $request, $tecnico) {
  //   $rolesRequest = $request->except(['_token', '_method']);
  //   foreach ($rolesRequest as $key => $value) {
  //     $roles[] = Role::where('id', $key)->first();
  //   }
  //   $tecnico = Tecnico::where('id', $tecnico)->first();
  //   if (!empty($roles)) {
  //     $tecnico->syncRoles($roles);
  //   } else {
  //     $tecnico->syncRoles(null);
  //   }
  //   return $tecnico->id;
  // }
  public function store(Request $request)
  {
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validação inválida',
        'errors' => $validator->errors(),
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
      $user->roles()->attach($request->id_grupo);

      //cadastro tecnico
      $inputs = $request->except('id_grupo', 'telefone', 'password');
      $inputs['id_telefone'] = $telefone->id;
      $inputs['id_user'] = $user->id;
      $tecnico = Tecnico::create($inputs);
      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'message' => 'Erro ao cadastrar',
        'errors' => [$e->getMessage()],
      ], 500);
    }
    if ($telefone && $tecnico) {
      return response()->json(['message' => 'success']);
    }
  }
  public function update(Request $request, $id)
  {
    $validator = $this->validateUpdate($request);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validação inválida',
        'errors' => $validator->errors(),
      ], 422);
    }
    try {
      DB::beginTransaction();

      $tecnico = Tecnico::find($id);
      $phone = Telefone::find($tecnico->id_telefone);
      $user = User::find($tecnico->id_user);

      $data = $request->all();
      $data['numero'] = $request->telefone['numero'];
      $data['codigo_area'] = $request->telefone['codigo_area'];

      $tecnico->update($data);
      $phone->update($data);
      $user->update($data);
      $user->roles()->sync($request->id_grupo);
      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'message' => 'Não foi possível alterar os dados.',
        'errors' => [$e->getMessage()],
      ], 500);
    }
    return response()->json(['message' => 'success']);
  }
  public function findAll()
  {
    return Tecnico::select(
      'tecnico.id',
      'tecnico.nome as nome_tecnico',
      'tecnico.cpf as cpf_tecnico',
      'tecnico.status'
    )->get();
  }
  public function findById($id)
  {
    $user = User::select('users.*')
      ->with(['roles' => function ($r) {
        $r->get(['id', 'name']);
      }])
      ->where('t.id', $id)
      ->join('tecnico as t', 't.id_user', 'users.id')
      ->first();
    $tecnico = Tecnico::select(
      'tecnico.id',
      'tecnico.nome',
      'tecnico.sobrenome',
      'tecnico.cpf',
      'tecnico.status',
      'u.email',
      'tecnico.numero_registro',
      DB::raw('CONCAT(\'(\', t.codigo_area, \') \', t.numero) as phone')
    )
      ->join('users as u', 'u.id', 'tecnico.id_user')
      ->join('telefone as t', 't.id', 'tecnico.id_telefone')
      ->find($id);

    if (sizeOf($user->roles) > 0) {
      $tecnico->grupo = $user->roles[0];
    }

    return response()->json($tecnico);
  }

  public function changePassword(Request $request, $id)
  {
    $validator = Validator::make($request->all(), [
      'senha' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'error',
        'errors' => $validator->errors(),
      ]);
    }

    try {
      $tecnico = Tecnico::find($id);

      User::find($tecnico->id_user)->update([
        'password' => bcrypt($request->senha),
      ]);

      return response()->json(['message' => 'success']);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'error',
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  public function getProfile()
  {
    $id = auth()->id();

    $tecnico = Tecnico::from('tecnico as tc')
      ->select(
        'u.name as nome',
        'tc.sobrenome',
        'u.email',
        'tc.cpf',
        DB::raw('CONCAT(\'(\', t.codigo_area, \') \', t.numero) as phone')
      )
      ->join('users as u', 'u.id', 'tc.id_user')
      ->join('telefone as t', 't.id', 'tc.id_telefone')
      ->where('u.id', $id)
      ->first();

    return response()->json($tecnico);
  }

  public function editProfile(Request $request)
  {
    $id = auth()->id();

    $user_data = [];
    $user_data['email'] = $request->email;
    $user_data['name'] = $request->nome;

    $tecn_data = $request->only(['nome', 'cpf', 'sobrenome']);

    try {
      DB::beginTransaction();

      User::find($id)->update($user_data);
      Tecnico::where('id_user', $id)->update($tecn_data);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();

      return response()->json([
        'message' => 'error',
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  public function editProfilePassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'senha' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'error',
        'errors' => $validator->errors(),
      ]);
    }

    $id = auth()->id();

    try {
      User::find($id)->update([
        'password' => bcrypt($request->senha),
      ]);

      return response()->json(['message' => 'success']);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'error',
        'errors' => [$e->getMessage()],
      ]);
    }
  }

  private function setStatus(bool $status, $id)
  {
    try {
      DB::beginTransaction();
      Tecnico::find($id)->update(['status' => $status]);
      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return array('response' => [
        'message' => 'error',
        'errors' => [$e->getMessage()],
      ], 'status' => 500);
    }
    return array('response' => ['message' => 'success'], 'status' => 200);
  }
  public function disable($id)
  {
    $result = $this->setStatus(false, $id);
    return response()->json($result['response'], $result['status']);
  }
  public function enable($id)
  {
    $result = $this->setStatus(true, $id);
    return response()->json($result['response'], $result['status']);
  }
}
