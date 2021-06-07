<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Talhao extends Model
{
  protected $table = 'talhao';
  protected $fillable = [
    'cultura',
    'relatorio',
  ];
}
