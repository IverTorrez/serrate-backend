<?php

namespace App\Http\Controllers;

use App\Models\Tribunal;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreTribunalRequest;
use App\Http\Requests\UpdateTribunalRequest;
use App\Http\Resources\TribunalCollection;

class TribunalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tribunal = Tribunal::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new TribunalCollection($tribunal);
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
    public function store(StoreTribunalRequest $request)
    {
        $estado=Estado::ACTIVO;
        $tribunal=Tribunal::create([
            'expediente'=>$request->expediente,
            'codnurejianuj'=>$request->codnurejianuj,
            'link_carpeta'=>$request->link_carpeta,
            'clasetribunal_id'=>$request->clasetribunal_id,
            'causa_id'=>$request->causa_id,
            'juzgado_id'=>$request->juzgado_id,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$tribunal
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tribunal $tribunal)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$tribunal
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tribunal $tribunal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTribunalRequest $request, Tribunal $tribunal)
    {
        $tribunal->update($request->only([
            'expediente',
            'codnurejianuj',
            'link_carpeta',
            'clasetribunal_id',
            'causa_id',
            'juzgado_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$tribunal
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tribunal $tribunal)
    {
        $tribunal->es_eliminado   =1;
         $tribunal->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$tribunal
        ];
        return response()->json($data);
    }
}
