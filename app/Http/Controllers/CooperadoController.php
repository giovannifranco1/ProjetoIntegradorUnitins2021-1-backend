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
    private $objCooperado;
    private $objPessoa;
    private $objTelefone;

    public function __construct()
    {
        $this->objCooperado = new Cooperado();
        $this->objPessoa = new Pessoa();
        $this->objTelefone = new Telefone();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|max:255',
            'email' => 'required|email',
            'cpf' => 'required|max:14|min:14'
        ]);
        if($validator->fails()){
            return response()->json(['message' => $validator->errors()]);
        }

        # cadastro pessoa
        $inputs = $request->all();
        $inputs['numero'] = $request->phone['numero'];
        $inputs['codigo_area'] = '2121';
        $inputs['status'] = true;
        $pessoa = $this->objPessoa->create($inputs);

        #cadastro cooperado
        dd($cooperado = $this->objCooperado->pessoa->telefone->create($inputs));
    }
}
