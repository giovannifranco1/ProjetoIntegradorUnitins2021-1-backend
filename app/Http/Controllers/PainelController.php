<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Tecnico;
use App\Models\Visita;
use Illuminate\Http\Request;

class PainelController extends Controller
{
    public function index(Request $request){
        dd(Tecnico::find(1));
        $pessoa = Tecnico::where('nome' , $request->tecnico)->first();
        $visita = Visita::where('id_tecnico', 'like', "%{$tecnico->id}%")
        ->orderBy('dia_visita' , 'desc')
        ->paginate(7);

        return response()->json(compact('visita'));
    }
}
