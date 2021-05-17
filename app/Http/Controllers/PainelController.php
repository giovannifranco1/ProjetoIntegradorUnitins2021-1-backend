<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Tecnico;
use App\Models\Visita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PainelController extends Controller
{
    public function index(Request $request){
        $visita = Visita::select('tecnico.nome','p_c.nome as nome_cooperado', 'propriedade.nome as nome_propriedade', 'visita.*')
            ->join('tecnico', 'tecnico.id', 'visita.id_tecnico')
            ->join('propriedade', 'propriedade.id', 'visita.id_propriedade')
            ->join('cooperado as c', 'c.id', 'propriedade.id_cooperado')
            ->join('pessoa as p_c', 'p_c.id', 'c.id_pessoa')
            ->where('p_c.nome','like',  "%{$request->nome_cooperado}%")
            ->where('propriedade.nome', 'like', "%{$request->nome_propriedade}%")
            ->where('visita.dia_visita' , 'like', "%{$request->dia_visita}%")
            ->where('visita.motivo_visita' ,'like' , "%{$request->motivo_visita}%")
            ->orderBy('visita.dia_visita' , 'desc')
            ->get();
        return response()->json($visita);
    }
}
