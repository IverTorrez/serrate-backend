<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClaseTribunalCollection;
use App\Models\ClaseTribunal;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreClaseTribunalRequest;
use App\Http\Requests\UpdateClaseTribunalRequest;

class ClaseTribunalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $claseTribunal = ClaseTribunal::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new ClaseTribunalCollection($claseTribunal);
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
    public function store(StoreClaseTribunalRequest $request)
    {
        $estado=Estado::ACTIVO;
        $claseTribunal=ClaseTribunal::create([
            'nombre'=>$request->nombre,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$claseTribunal
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClaseTribunal $claseTribunal)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$claseTribunal
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClaseTribunal $claseTribunal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClaseTribunalRequest $request, ClaseTribunal $claseTribunal)
    {
        $claseTribunal->update($request->only([
            'nombre',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$claseTribunal
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClaseTribunal $claseTribunal)
    {
        $claseTribunal->es_eliminado   =1;
         $claseTribunal->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$claseTribunal
        ];
        return response()->json($data);
    }
}
