<?php

namespace App\Http\Controllers;

use App\Models\Cooperado;
use App\Models\Pessoa;
use App\Models\Tecnico;
use App\Models\Telefone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CooperadoController extends Controller
{
    public function __construct()
    {

    }
    private function companyValidator($request){
        $validator = Validator::make($request->all(), [
            'nome' => 'required|max:255',
            'email' => 'required|email',
            'cpf' => 'required|max:14|min:14'
        ]);
        return $validator;
    }
    public function store(Request $request)
    {
        $validator = $this->companyValidator($request);
        if($validator->fails() ) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors'  => $validator->errors()
            ], 422);
        }
        $inputs = $request->all();
        $inputs['numero'] = $request->phone['numero'];
        $inputs['codigo_area'] = '2121';

        # cadastro telefone
        $telefone = Telefone::create($inputs);
        $inputs['id_telefone'] = $telefone->id;
        # cadastro pessoa
        $pessoa = Pessoa::create($inputs);
        # cadastro cooperado
        $inputs['id_pessoa'] = $pessoa->id;
        $cooperado= Cooperado::create($inputs);


    }
    public function findAll(){
        return Cooperado::select('cooperado.id','p.nome as nome_cooperado' , 'p.cpf as cpf_cooperado',)
            ->join('pessoa as p' ,'p.id', 'cooperado.id_pessoa')
            ->get();
    }
}
