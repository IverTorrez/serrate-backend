<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Filters\MateriaFilter;
use App\Http\Resources\MateriaCollection;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreMateriaRequest;
use App\Http\Requests\UpdateMateriaRequest;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $materias = Materia::where('es_eliminado', 0)
        //                    ->where('estado', Estado::ACTIVO)
        //                    ->paginate();
        // return new MateriaCollection($materias);

        $filter = new MateriaFilter();
        $queryItems = $filter->transform($request);
        $materias= Materia::where($queryItems)
                           ->where('estado', Estado::ACTIVO)
                           ->where('es_eliminado', 0);

        return new MateriaCollection($materias->paginate(10)->appends($request->query()));
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
    public function store(StoreMateriaRequest $request)
    {
        $estado=Estado::ACTIVO;
        $materia=Materia::create([
            'nombre'=>$request->nombre,
            'abreviatura'=>$request->abreviatura,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);

         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$materia
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Materia $materia)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$materia
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Materia $materia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMateriaRequest $request, Materia $materia)
    {
        // $materia->nombre        =$request->nombre;
        // $materia->abreviatura   =$request->abreviatura;
        // $materia->save();
        $materia->update($request->only([
                                   'nombre',
                                   'abreviatura',
                                   'estado',
                                   'es_eliminado']));
        $data=[
            'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data'=>$materia
        ];
        return response()->json($data);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Materia $materia)
    {
         $materia->es_eliminado   =1;
         $materia->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$materia
        ];
        return response()->json($data);
    }
}
