<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
  public function __construct()
  {

  }
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'nome' => 'required',
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
    $roles = DB::table($table)
      ->select('permission_id as permission', 'r.name')
      ->join('roles as r', 'r.id' , $table.'.role_id')
      ->get();
    return response()->json($roles);
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
        'errors'  => $validator->errors()
      ], 422);
    }
    $data['name'] = $request->nome;
    try {
      DB::beginTransaction();
      $role = Role::create($data);
      $role->permissions()->attach($request->permissions);
      DB::commit();
    } catch (Exception $e) {
      dd($e);
      DB::rollBack();
      return response()->json(['message' => 'Erro ao cadastrar'], 400);
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
        'errors'  => $validator->errors()
      ], 422);
    }

    $data['name'] = $request->nome;
    $role = Role::find($id);
    try {
      DB::beginTransaction();
      $role->update($data);
      $role->permissions()->sync($request->permissions);
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
  public function findAll(){
    $role = Role::select('roles.nome')
    ->with(['permissions' => function($r) {
      $permissions = [];
      foreach($r->get(['id']) as $value){
        $permissions[] = $value->id;
      }
      $r = $permissions;
    }])
    ->get();
    dd($role);
  }
}
