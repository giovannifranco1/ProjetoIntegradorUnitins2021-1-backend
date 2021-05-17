<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivoVisita extends Model
{
    protected $fillable = ['nome'];
    protected $table = 'motivo_visita';
    public $timestamps = false;
}
