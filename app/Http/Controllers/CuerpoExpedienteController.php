<?php

namespace App\Http\Controllers;

use App\Models\CuerpoExpediente;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreCuerpoExpedienteRequest;
use App\Http\Requests\UpdateCuerpoExpedienteRequest;
use App\Http\Resources\CuerpoExpedienteCollection;

class CuerpoExpedienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cuerpoExpediente = CuerpoExpediente::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new CuerpoExpedienteCollection($cuerpoExpediente);
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
    public function store(StoreCuerpoExpedienteRequest $request)
    {
        $estado=Estado::ACTIVO;
        $cuerpoExpediente=CuerpoExpediente::create([
            'nombre'=>$request->nombre,
            'link_cuerpo'=>$request->link_cuerpo,
            'tribunal_id'=>$request->tribunal_id,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$cuerpoExpediente
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(CuerpoExpediente $cuerpoExpediente)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$cuerpoExpediente
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CuerpoExpediente $cuerpoExpediente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCuerpoExpedienteRequest $request, CuerpoExpediente $cuerpoExpediente)
    {
        $cuerpoExpediente->update($request->only([
            'nombre',
            'link_cuerpo',
            'tribunal_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$cuerpoExpediente
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CuerpoExpediente $cuerpoExpediente)
    {
        $cuerpoExpediente->es_eliminado   =1;
         $cuerpoExpediente->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$cuerpoExpediente
        ];
        return response()->json($data);
    }
}
