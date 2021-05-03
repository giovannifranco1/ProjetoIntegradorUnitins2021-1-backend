<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefone extends Model
{
    protected $fillable = ['codigo_area', 'numero'];
    protected $table = 'telefone';
}
