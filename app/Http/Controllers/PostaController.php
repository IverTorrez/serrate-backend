<?php

namespace App\Http\Controllers;

use App\Models\Posta;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Http\Resources\PostaCollection;
use App\Http\Requests\StorePostaRequest;
use App\Http\Requests\UpdatePostaRequest;

class PostaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posta = Posta::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new PostaCollection($posta);
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
    public function store(StorePostaRequest $request)
    {
        $posta=Posta::create([
            'nombre'=>$request->nombre,
            'numero_posta'=>$request->numero_posta,
            'plantilla_id'=>$request->plantilla_id,
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$posta
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Posta $posta)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$posta
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Posta $posta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostaRequest $request, Posta $posta)
    {
        $posta->update($request->only([
            'nombre',
            'numero_posta',
            'plantilla_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$posta
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Posta $posta)
    {
        $posta->es_eliminado   =1;
         $posta->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$posta
        ];
        return response()->json($data);
    }
}
