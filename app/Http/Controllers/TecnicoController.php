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

    public function __construct()
    {
        $this->objTecnico = new Tecnico();
        $this->objPessoa = new Pessoa();
        $this->objTelefone = new Telefone();
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nome' => 'required|max:255',
            'email' => 'required|email',
            'cpf' => 'required|max:14|min:14'
        ]);
        if($validator->fails()){
            return response()->json(['message' => $validator->errors()]);
        }
        $inputs = $request->all();
        $inputs['numero'] = $request->phone['numero'];
        $inputs['codigo_area'] = $request->phone['codigo_area'];

        //cadastro telefone
        $telefone = $this->objTelefone->create($inputs);

        //cadastro pessoa
        $inputs['id_telefone'] = $telefone->id;
        $inputs['status'] = true;
        $pessoa = $this->objPessoa->create($inputs);

        //cadastro tecnico
        $inputs['numero_registro'] = $request->register;
        $inputs['senha'] = bcrypt($request->password);
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
