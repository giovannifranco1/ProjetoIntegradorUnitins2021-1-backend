<?php

namespace App\Http\Controllers;

use App\Mail\SendMailUser;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RecuperarSenhaController extends Controller
{
  public function __invoke(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }
    $user = User::where('email', $request->email)->first();
    if (empty($user)) {
      return response()->json(['message' => 'fail', 'errors' => ['Email não existe']], 401);
    }

    $token = mt_rand(100000, 999999);

    DB::table('password_resets')->insert([
      'email' => $request->email,
      'token' => $token,
      'created_at' => new DateTime(),
    ]);

    Mail::to($user->email)->send(new SendMailUser($user, $token));
    return response()->json(['message' => 'Enviado com sucesso'], 200);
  }
  public function alterarSenha(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'token' => 'required',
      'email' => 'required|email',
      'password' => 'required|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }

    $password_reset = DB::table('password_resets')
      ->where('token', $request->token)
      ->where('email', $request->email)
      ->orderBy('created_at', 'desc')
      ->first();

    if (empty($password_reset)) {
      return response()->json([
        'message' => 'fail', 'errors' => ['token inválido para este usuario'],
      ], 401);
    }

    if (!(strtotime(date('Y-m-d h:i', strtotime("+1 days", strtotime($password_reset->created_at)))) <= strtotime(date('Y-m-d h:i')))) {
      return response()->json(['message' => 'fail', 'errors' => ['token expirado']], 400);
    }
    $user = User::where('email', $password_reset->email)->first();
    try {
      DB::beginTransaction();
      $password_reset->token = null;

      DB::table('password_resets')
        ->where('token', $request->token)
        ->where('email', $request->email)
        ->delete();

      $user->update([
        'password' => bcrypt($request->password),
      ]);
      DB::commit();
    } catch (\Throwable$th) {
      return response()->json([
        'message' => 'fail', 'errors' => ['Erro ao alterar a senha'],
      ], 400);
    }
  }

}
