<?php

namespace App\Http\Controllers\Admin\Autenticacao;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;



class RoleController extends Controller
{
  public function __construct()
  {
    $this->middleware('permission:Autenticação');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $roles = Role::all();
    return response()->json(compact('roles'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();
    try {
      DB::beginTransaction();
      Role::create($data);
      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json(['message' => 'Erro ao cadastrar'], 400);
    }
    return response()->json(['message' => 'Editado com sucesso!']);
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
    $data = $request->all();
    $role = Role::find($id);
    try {
      DB::beginTransaction();
      $role->update($data);
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
    //
    $role = Role::find($id);
    $role->delete();
    return redirect()->route('role.index')->with('status', 'Perfil Removido com Sucesso!');
  }

  public function permissions($role)
  {

    $role = Role::where('id', $role)->first();
    $permissions = Permission::all();

    foreach ($permissions as $permission) {

      if ($role->hasPermissionTo($permission->name)) {
        $permission->can = true;
      } else {
        $permission->can = false;
      }
    }
    return view('admin.autenticacao.roles.permissions', ['role' => $role, 'permissions' => $permissions]);
  }

  public function permissionsSync(Request $request, $role)
  {
    $permissionsRequest = $request->except(['_token', '_method']);

    foreach ($permissionsRequest as $key => $value) {
      $permissions[] = Permission::where('id', $key)->first();
    }
    $role = Role::where('id', $role)->first();
    if (!empty($permissions)) {
      $role->syncPermissions($permissions);
    } else {
      $role->syncPermissions(null);
    }
    return redirect()->route('role.permissions', compact('role'))->with('status', 'Sincronizado');
  }
}
