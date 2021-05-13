<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cooperado extends Model
{
    protected $fillable  = ['id_pessoa'];
    protected $table = 'cooperado';

    public function pessoa(){
        return $this->belongsTo(Pessoa::class);
    }
}
