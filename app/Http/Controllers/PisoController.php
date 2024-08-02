<?php

namespace App\Http\Controllers;

use App\Models\Piso;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\PisoCollection;
use App\Http\Requests\StorePisoRequest;
use App\Http\Requests\UpdatePisoRequest;

class PisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $piso = Piso::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate(10);
        return new PisoCollection($piso);
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
    public function store(StorePisoRequest $request)
    {
        $estado=Estado::ACTIVO;
        $piso=Piso::create([
            'nombre'=>$request->nombre,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$piso
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Piso $piso)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$piso
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Piso $piso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePisoRequest $request, Piso $piso)
    {
        $piso->update($request->only([
            'nombre',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$piso
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Piso $piso)
    {
        $piso->es_eliminado   =1;
         $piso->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$piso
        ];
        return response()->json($data);
    }
}
