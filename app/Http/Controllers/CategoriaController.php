<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Models\Categoria;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\CategoriaCollection;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoria = Categoria::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new CategoriaCollection($categoria);
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
    public function store(StoreCategoriaRequest $request)
    {
        $estado=Estado::ACTIVO;
        $categoria=Categoria::create([
            'nombre'=>$request->nombre,
            'abreviatura'=>$request->abreviatura,
            'estado'=>$estado,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$categoria
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$categoria
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->only([
            'nombre',
            'abreviatura',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$categoria
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        $categoria->es_eliminado   =1;
         $categoria->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$categoria
        ];
        return response()->json($data);
    }
}
