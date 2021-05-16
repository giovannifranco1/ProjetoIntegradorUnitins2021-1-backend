<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Tecnico extends Authenticatable implements JWTSubject
{
  protected $fillable = [
    'nome',
    'cpf',
    'sobrenome',
    'numero_registro',
    'id_telefone',
    'id_user',
    'status'
  ];
  protected $table = 'tecnico';

  public function getJWTIdentifier()
  {
    return $this->getKey();
  }
  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    return [];
  }
}
