<?php

namespace App\Http\Controllers;

use App\Mail\SendMailUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecuperarSenhaController extends Controller
{
  public function __invoke(Request $request)
  {
    $user = User::where('email', $request->email)->first();
    if (empty($user)) {
      return response()->json(['message' => 'Email não existe'], 401);
    }

    $codigo = mt_rand(100000, 999999);
    Mail::to($user->email)->send(new SendMailUser($user, $codigo));
    return response()->json(['message' => 'Enviado com sucesso'], 200);
  }
}
