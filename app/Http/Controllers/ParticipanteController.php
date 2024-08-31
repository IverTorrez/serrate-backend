<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Models\Participante;
use Illuminate\Http\Request;
use App\Http\Resources\ParticipanteCollection;
use App\Http\Requests\StoreParticipanteRequest;
use App\Http\Requests\UpdateParticpanteRequest;
use App\Services\ParticipanteService;

class ParticipanteController extends Controller
{
    protected $participanteService;

    public function __construct(ParticipanteService $participanteService)
    {
        $this->participanteService = $participanteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Participante::active();
        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }
        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }
        $perPage = $request->input('perPage', 10);
        $participantes = $query->paginate($perPage);

        return new ParticipanteCollection($participantes);
    }

    public function listadoPorCausa(Request $request, $causaId)
    {
        $query = Participante::active()
            ->where('causa_id', $causaId);
        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }
        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }
        $perPage = $request->input('perPage', 10);
        $participantes = $query->paginate($perPage);

        return new ParticipanteCollection($participantes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParticipanteRequest $request)
    {
        $data = ([
            'nombres' => $request->nombres,
            'tipo' => $request->tipo,
            'foja' => $request->foja,
            'ultimo_domicilio' => $request->ultimo_domicilio,
            'causa_id' => $request->causa_id
        ]);
        $participante = $this->participanteService->store($data);
        return response()
            ->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $participante
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Participante $participante)
    {
        $participante = $this->participanteService->obtenerUno($participante->id);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $participante
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Participante $participante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParticpanteRequest $request, Participante $participante)
    {
        $data = $request->only([
            'nombres',
            'tipo',
            'foja',
            'ultimo_domicilio',
            'causa_id'
        ]);
        $participante = $this->participanteService->update($data, $participante->id);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $participante
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participante $participante)
    {
        $participante = $this->participanteService->destroy($participante->id);
        return response()->json([
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $participante
        ]);
    }
}
