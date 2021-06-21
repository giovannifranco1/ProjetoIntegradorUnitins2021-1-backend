<?php

namespace App\Http\Controllers;

use App\Models\FotoTalhao;
use App\Models\Talhao;
use App\Models\Tecnico;
use App\Models\Visita;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VisitaController extends Controller
{
  private function companyValidator($request)
  {
    $validator = Validator::make($request->all(), [
      'horaEstimada' => 'required|date',
      'dia_visita' => 'required|date',
      'id_user' => 'required',
      'id_propriedade' => 'required',
      'motivo_visita' => 'required',
    ]);
    return $validator;
  }

  private function validateUpdate($request)
  {
    $validator = Validator::make($request->all(), [
      'horaEstimada' => 'required|date',
      'dia_visita' => 'required|date',
      'motivo_visita' => 'required|string',
    ]);
    return $validator;
  }

  public function findById($id)
  {
    $visita = Visita::select('visita.*', 'p.nome as cooperado', 'pr.nome as propriedade')
      ->join('propriedade as pr', 'pr.id', 'visita.id_propriedade')
      ->join('cooperado as c', 'c.id', 'pr.id_cooperado')
      ->join('pessoa as p', 'p.id', 'c.id_pessoa')
      ->where('visita.id', $id)
      ->first();

    $visita->talhoes = Talhao::where('id_visita', $id)->get();
    foreach ($visita->talhoes as $key => $talhao) {
      $visita->talhoes[$key]->foto_talhao = FotoTalhao::where('id_talhao', $talhao->id)->get();
    }

    return response()->json($visita);
  }
  private function transformUrl($url)
  {
    return str_replace('\s+', '-', strtolower($url));
  }

  public function findByTecnico($id)
  {
    $visitas = Visita::select('visita.*', 'p.nome as propriedade')
      ->join('propriedade as p', 'p.id', 'visita.id_propriedade')
      ->join('tecnico as t', 't.id', 'visita.id_tecnico')
      ->join('users as u', 'u.id', 't.id_user')
      ->where('u.id', $id)
      ->get();

    return response()->json($visitas);
  }
  public function store(Request $request)
  {
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }
    $data = $request->all();
    $data['dia_visita'] = new DateTime($data['dia_visita']);
    $data['horario_estimado_visita'] = new DateTime($data['horaEstimada']);
    try {
      DB::beginTransaction();
      $tecnico = Tecnico::select('id')
        ->where('id_user', $request->id_user)
        ->first();

      $data['id_tecnico'] = $tecnico->id;

      Visita::create($data);

      DB::commit();
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()],
      ], 400);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }
  public function update(Request $request, $id)
  {
    $visitas = Visita::select('visita.id')
      ->join('tecnico as t', 't.id', 'visita.id_tecnico')
      ->join('users as u', 'u.id', 't.id_user')
      ->where('visita.id', $id)
      ->where('u.id', Auth::user()->id)
      ->first();

    if (empty($visitas)) {
      return response()->json(null, 401);
    }

    $validator = $this->validateUpdate($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }
    $data = $request->except(['talhoes']);
    $data['dia_visita'] = new DateTime($data['dia_visita']);
    $data['horario_estimado_visita'] = new DateTime($data['horaEstimada']);
    $visita = Visita::find($id);
    try {
      DB::beginTransaction();
      $talhoes = $request->talhoes;
      if (!empty($talhoes)) {
        for ($i = 0; $i < count($talhoes); $i++) {
          $talhao = $talhoes[$i];
          $talhao_create = Talhao::create([
            "cultura" => $talhao['cultura'],
            "relatorio" => $talhao['relatorio'],
            "id_visita" => $id,
          ]);
          for ($x = 0; $x < count($request->imagem[$i]); $x++) {
            $imagem = $request->imagem[$i][$x];
            $name = $this->transformUrl($imagem->getClientOriginalName());
            $extension = $imagem->extension();
            $crypto = md5($name . date('HisYmd')) . '.' . $extension;
            $local = "Visitas/{$id}/{$talhao_create->id}";
            $upload = $imagem->storeAs($local, $crypto, 'public');
            $foto_talhao = [
              'nome' => $name,
              'imagem' => $upload,
              'id_talhao' => $talhao_create->id,
            ];
            FotoTalhao::create($foto_talhao);
          }
        }
      }
      $visita->update($data);
      DB::commit();
    } catch (Exception $e) {
      dd($e);
      DB::rollBack();
      return response()->json([
        'message' => 'fail',
        'errors' => [$e->getMessage()],
      ], 400);
    }
    return response()->json(['message' => 'Editado com sucesso!']);
  }

  public function cancel(Request $request, $id)
  {
    $data = $request->all();
    $visita = Visita::find($id);
    try {
      $visita->update($data);
    } catch (Exception $e) {
      return response()->json([
        'message' => 'Erro ao Cancelar',
        'errors' => [$e->getMessage()],
      ], 500);
    }
    return response()->json(['message' => 'Cancelado com sucesso!']);
  }
}
