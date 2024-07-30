<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Models\TipoLegal;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\TipoLegalCollection;
use App\Http\Requests\StoreTipoLegalRequest;
use App\Http\Requests\UpdateTipoLegalRequest;

class TipoLegalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tipoLegal = TipoLegal::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new TipoLegalCollection($tipoLegal);
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
    public function store(StoreTipoLegalRequest $request)
    {
        $estado=Estado::ACTIVO;
        $tipoLegal=TipoLegal::create([
            'nombre'=>$request->nombre,
            'abreviatura'=>$request->abreviatura,
            'materia_id'=>$request->materia_id,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$tipoLegal
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoLegal $tipoLegal)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$tipoLegal
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TipoLegal $tipoLegal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTipoLegalRequest $request, TipoLegal $tipoLegal)
    {
        $tipoLegal->update($request->only([
            'nombre',
            'abreviatura',
            'estado',
            'materia_id',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$tipoLegal
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TipoLegal $tipoLegal)
    {
        $tipoLegal->es_eliminado   =1;
         $tipoLegal->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$tipoLegal
        ];
        return response()->json($data);
    }
}
