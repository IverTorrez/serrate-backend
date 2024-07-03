<?php

namespace App\Http\Controllers;

use App\Models\AvancePlantilla;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreAvancePlantillaRequest;
use App\Http\Requests\UpdateAvancePlantillaRequest;
use App\Http\Resources\AvancePlantillaCollection;

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
            'message'=>'Registro creado exitosamente',
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
            'message'=>'Resultado obtenido exitosamente',
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
        'message'=>'Registro actualizado correctamente',
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
            'message'=>'Registro eliminado correctamente',
            'data'=>$avancePlantilla
        ];
        return response()->json($data);
    }
}
