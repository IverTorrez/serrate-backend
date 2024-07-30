<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Models\Participante;
use Illuminate\Http\Request;
use App\Http\Resources\ParticipanteCollection;
use App\Http\Requests\StoreParticipanteRequest;
use App\Http\Requests\UpdateParticpanteRequest;

class ParticipanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $participante = Participante::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new ParticipanteCollection($participante);
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
        $participante=Participante::create([
            'nombres'=>$request->nombres,
            'tipo'=>$request->tipo,
            'foja'=>$request->foja,
            'ultimo_domicilio'=>$request->ultimo_domicilio,
            'causa_id'=>$request->causa_id,
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$participante
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Participante $participante)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$participante
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
        $participante->update($request->only([
            'nombres',
            'tipo',
            'foja',
            'ultimo_domicilio',
            'causa_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$participante
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participante $participante)
    {
        $participante->es_eliminado   =1;
         $participante->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$participante
        ];
        return response()->json($data);
    }
}
