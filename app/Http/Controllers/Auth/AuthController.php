<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth:api', ['except' => ['login']]);
  }

  // Validator Method
  protected function companyValidator($request)
  {
    return Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required'
    ]);
  }
  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request){
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors'  => $validator->errors()
      ], 422);
    }
    $credentials = $request->all();
    $token = auth::attempt($credentials);
    if (!$token) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    return $this->respondWithToken($token);
  }
  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {
    auth::logout();
    return response()->json(['message' => 'Successfully logged out']);
  }
  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    $user = User::select('name', 'id', 'email')->find(auth()->id());
    $permissoes = User::select('name', 'id', 'email')
      ->find(auth()->id())
      ->getPermissionsViaRoles()
      ->mapWithKeys(function($t){
      return [
        $t['id'] => $t['id']
      ];
    });
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth::factory()->getTTL() * 60,
      'user' => [
          'usuario' => $user,
          'permissoes' => $permissoes
        ]
    ]);
  }
}
