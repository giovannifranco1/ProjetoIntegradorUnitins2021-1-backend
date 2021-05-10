<?php

namespace App\Http\Controllers;

use App\Models\Pessoa;
use App\Models\Tecnico;
use App\Models\Telefone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class TecnicoController extends Controller
{
    private $objPessoa;
    private $objTecnico;
    private $objTelefone;

    private function companyValidator($request){
        $validator = Validator::make($request->all(), [
            'nome' => 'required|max:255',
            'email' => 'required|email|unique:pessoa',
            'sobrenome' => 'required',
            'cpf' => 'required|max:14|min:14',
            'senha' => 'required|min:8',
            'numero_registro' => 'required',
        ]);
        return $validator;
    }
    public function __construct()
    {
        $this->objTecnico = new Tecnico();
        $this->objPessoa = new Pessoa();
        $this->objTelefone = new Telefone();
    }
    public function store(Request $request){
        $validator = $this->companyValidator($request);
        if($validator->fails() ) {
            return response()->json([
                'message' => 'Validation Failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $inputs = $request->all();
        $inputs['numero'] = $request->telefone['numero'];
        $inputs['codigo_area'] = $request->telefone['codigo_area'];

        //cadastro telefone
        $telefone = $this->objTelefone->create($inputs);

        //cadastro pessoa
        $inputs['id_telefone'] = $telefone->id;
        $inputs['status'] = true;
        $pessoa = $this->objPessoa->create($inputs);

        //cadastro tecnico
        $inputs['senha'] = bcrypt($request->senha);
        $inputs['id_pessoa'] = $pessoa->id;

        $tecnico = $this->objTecnico->create($inputs);

        if($telefone && $pessoa && $tecnico){
            return response()->json(['status' => 'success']);
        }
    }

    public function findAll(){
        return Tecnico::select('tecnico.id','p.nome as nome_tecnico' , 'p.cpf as cpf_tecnico',)
            ->join('pessoa as p' ,'p.id', 'tecnico.id_pessoa')
            ->get();
    }
}
