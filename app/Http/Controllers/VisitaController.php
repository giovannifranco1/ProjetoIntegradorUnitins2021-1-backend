<?php

namespace App\Http\Controllers;

use App\Models\FotoTalhao;
use App\Models\Talhao;
use App\Models\Tecnico;
use App\Models\Visita;
use DateTime;
use Exception;
use Illuminate\Http\Request;
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
      'imagem' => 'mimetypes:image/*',
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

    return response()->json($visita);
  }
  private function transformUrl($url)
  {
    return str_replace('\s+', '-', strtolower($url));
  }

  public function findByTecnico($id) {
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
        'errors' => [$e->getMessage()]
      ], 400);
    }
    return response()->json(['message' => 'Cadastrado com sucesso!']);
  }
  public function update(Request $request, $id)
  {
    $validator = $this->companyValidator($request);
    if ($validator->fails()) {
      return response()->json([
        'message' => 'Validation Failed',
        'errors' => $validator->errors(),
      ], 422);
    }
    $data = $request->except(['imagem', 'observacao', 'cultura', 'relatorio']);
    $talhao = $request->only(['cultura', 'relatorio']);
    $visita = Visita::find($id);
    try {
      DB::beginTransaction();
      if ($request->hasFile('imagem') && $request->file('imagem')->isValid()) {
        $name = $this->transformUrl($request->arquivo->getClientOriginalName());
        $extension = $request->arquivo->extension();
        $crypto = md5($name . date('HisYmd')) . '.' . $extension;
        $local = "Visitas/{$visita->id}";
        $upload = $request->arquivo->storeAs($local, $crypto, 'public');
      }
      $visita->update($data);
      $talhao = Talhao::create($talhao);
      $foto_talhao['name'] = $name;
      $foto_talhao['imagem'] = $upload;
      $foto_talhao['id_talhao'] = $talhao->id;
      FotoTalhao::create($foto_talhao);
    } catch (Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'Erro ao editar',
        'errors' => [$e->getMessage()],
      ], 500);
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
