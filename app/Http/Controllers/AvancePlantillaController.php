<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\AvancePlantilla;
use App\Http\Resources\AvancePlantillaCollection;
use App\Http\Requests\StoreAvancePlantillaRequest;
use App\Http\Requests\UpdateAvancePlantillaRequest;

class AvancePlantillaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $avancePlantilla = AvancePlantilla::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new AvancePlantillaCollection($avancePlantilla);
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
    public function store(StoreAvancePlantillaRequest $request)
    {
        $avancePlantilla=AvancePlantilla::create([
            'nombre'=>$request->nombre,
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$avancePlantilla
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(AvancePlantilla $avancePlantilla)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$avancePlantilla
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AvancePlantilla $avancePlantilla)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAvancePlantillaRequest $request, AvancePlantilla $avancePlantilla)
    {
        $avancePlantilla->update($request->only([
            'nombre',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$avancePlantilla
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AvancePlantilla $avancePlantilla)
    {
        $avancePlantilla->es_eliminado   =1;
         $avancePlantilla->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$avancePlantilla
        ];
        return response()->json($data);
    }
}
