<?php

namespace App\Http\Controllers;

use App\Models\AgendaApunte;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreAgendaApunteRequest;
use App\Http\Requests\UpdateAgendaApunteRequest;
use App\Http\Resources\AgendaApunteCollection;

class AgendaApunteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agendaApunte = AgendaApunte::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new AgendaApunteCollection($agendaApunte);
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
    public function store(StoreAgendaApunteRequest $request)
    {
        $estado=Estado::ACTIVO;
        $agendaApunte=AgendaApunte::create([
            'detalle_apunte'=>$request->detalle_apunte,
            'fecha_inicio'=>$request->fecha_inicio,
            'fecha_fin'=>$request->fecha_fin,
            'color'=>$request->color,
            'causa_id'=>$request->causa_id,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$agendaApunte
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(AgendaApunte $agendaApunte)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$agendaApunte
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AgendaApunte $agendaApunte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgendaApunteRequest $request, AgendaApunte $agendaApunte)
    {
        $agendaApunte->update($request->only([
            'detalle_apunte',
            'fecha_inicio',
            'fecha_fin',
            'color',
            'causa_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$agendaApunte
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgendaApunte $agendaApunte)
    {
        $agendaApunte->es_eliminado   =1;
         $agendaApunte->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$agendaApunte
        ];
        return response()->json($data);
    }
}
