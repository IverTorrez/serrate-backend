<?php

namespace App\Http\Controllers;

use App\Models\Distrito;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreDistritoRequest;
use App\Http\Requests\UpdateDistritoRequest;
use App\Http\Resources\DistritoCollection;

class DistritoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $distrito = Distrito::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new DistritoCollection($distrito);
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
    public function store(StoreDistritoRequest $request)
    {
        $estado=Estado::ACTIVO;
        $distrito=Distrito::create([
            'nombre'=>$request->nombre,
            'abreviatura'=>$request->abreviatura,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$distrito
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Distrito $distrito)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$distrito
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distrito $distrito)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDistritoRequest $request, Distrito $distrito)
    {
        $distrito->update($request->only([
            'nombre',
            'abreviatura',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$distrito
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distrito $distrito)
    {
        $distrito->es_eliminado   =1;
         $distrito->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$distrito
        ];
        return response()->json($data);
    }
}
