<?php

namespace App\Http\Controllers;

use App\Models\Juzgado;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\JuzgadoCollection;
use App\Http\Requests\StoreJuzgadoRequest;
use App\Http\Requests\UpdateJuzgadoRequest;

class JuzgadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $juzgado = Juzgado::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new JuzgadoCollection($juzgado);
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
    public function store(StoreJuzgadoRequest $request)
    {
        $estado=Estado::ACTIVO;
        $juzgado=Juzgado::create([
            'nombre_numerico'=>$request->nombre_numerico,
            'jerarquia'=>$request->jerarquia,
            'materia_juzgado'=>$request->materia_juzgado,
            'coordenadas'=>$request->coordenadas,
            'foto_url'=>$request->foto_url,
            'contacto1'=>$request->contacto1,
            'contacto2'=>$request->contacto2,
            'contacto3'=>$request->contacto3,
            'contacto4'=>$request->contacto4,
            'distrito_id'=>$request->distrito_id,
            'piso_id'=>$request->piso_id,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$juzgado
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Juzgado $juzgado)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$juzgado
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Juzgado $juzgado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJuzgadoRequest $request, Juzgado $juzgado)
    {
        $juzgado->update($request->only([
            'nombre_numerico',
            'jerarquia',
            'materia_juzgado',
            'coordenadas',
            'foto_url',
            'contacto1',
            'contacto2',
            'contacto3',
            'contacto4',
            'distrito_id',
            'piso_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$juzgado
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Juzgado $juzgado)
    {
        $juzgado->es_eliminado   =1;
         $juzgado->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$juzgado
        ];
        return response()->json($data);
    }
}
