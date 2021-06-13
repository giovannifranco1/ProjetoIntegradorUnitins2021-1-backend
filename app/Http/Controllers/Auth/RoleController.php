<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'nome' => 'required',
      'permissoes' => 'array',
      'permissoes.*' => 'integer',
    ]);
    return $validator;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $table = 'role_has_permissions';

    $user = User::select('name', 'id', 'email')
      ->find(Auth::user()->id)
      ->getPermissionsViaRoles()
      ->mapWithKeys(function ($t) {
        return [
          $t['id'] => $t['id'],
        ];
      });
    $permission_user = [];
    foreach ($user as $key => $p) {
      $permission_user[$key] = $p;
    }
    $roles = Role::select('id', 'name')->get();
    $permissions = DB::table($table)->get();

    $result = [];
    foreach ($roles as $r) {
      $array_roles = [];
      $role = ['id' => $r->id, 'nome' => $r->name, 'permissoes' => []];
      foreach ($permissions as $key => $p) {
        if ($p->role_id == $r->id) {
          $role['permissoes'][] = $p->permission_id;
          $array_roles[$key] = $p->permission_id;
        }
      }
      if (empty(array_diff($array_roles, $permission_user))) {
        $result[] = $role;
      }
    }
    return response()->json($result);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }
    $data['name'] = $request->nome;

    $permissoes = User::select('name', 'id', 'email')
      ->find(Auth::user()->id)
      ->getPermissionsViaRoles()
      ->mapWithKeys(function ($t) {
        return [
          $t['id'] => $t['id'],
        ];
      });
    $permission_user = [];
    foreach ($permissoes as $key => $p) {
      $permission_user[$key] = $p;
    }
    if (!empty(array_diff($request->permissoes, $permission_user))) {
      return response()->json([
        'message' => 'fail',
        'errors' => ["você não tem privilégio",
        ]], 401);
    }

    try {
      DB::beginTransaction();

      $role = Role::create($data);
      $role->permissions()->attach($request->permissoes);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()],
      ], 400);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }
  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validator = $this->companyValidator($request);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }

    $data['name'] = $request->nome;
    $role = Role::find($id);
    try {
      DB::beginTransaction();
      $role->update($data);
      $role->permissions()->sync($request->permissoes);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao editar'], 400);
    }
    return response()->json(['message' => 'Editado com sucesso!']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $role = Role::find($id);
    try {
      DB::beginTransaction();
      $role->delete();
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao Remover'], 400);
    }
    return response()->json(['message' => 'Removido com sucesso!']);
  }

  public function permissions($role)
  {
    $role = Role::where('id', $role)->first();
    $permissions = Permission::all();

    foreach ($permissions as $permission) {
      $permission->can = $role->hasPermissionTo($permission->name);
    }
    return view('admin.autenticacao.roles.permissions', ['role' => $role, 'permissions' => $permissions]);
  }
  public function findAll()
  {
    $role = Role::select('roles.nome')
      ->with(['permissions' => function ($r) {
        $permissions = [];
        foreach ($r->get(['id']) as $value) {
          $permissions[] = $value->id;
        }
        $r = $permissions;
      }])
      ->get();

    dd($role);
  }
}
