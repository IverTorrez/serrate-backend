<?php

namespace App\Http\Controllers;

use App\Constants\Estado;
use App\Enums\MessageHttp;
use App\Models\CausaPosta;
use Illuminate\Http\Request;
use App\Http\Resources\CausaPostaCollection;
use App\Http\Requests\StoreCausaPostaRequest;
use App\Http\Requests\UpdateCausaPostaRequest;

class CausaPostaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $causaPosta = CausaPosta::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new CausaPostaCollection($causaPosta);
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
    public function store(StoreCausaPostaRequest $request)
    {
        $causaPosta=CausaPosta::create([
            'nombre'=>$request->nombre,
            'numero_posta'=>$request->numero_posta,
            'copia_nombre_plantilla'=>$request->copia_nombre_plantilla,
            'tiene_informe'=>0,
            'causa_id'=>$request->causa_id,
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$causaPosta
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(CausaPosta $causaPosta)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$causaPosta
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CausaPosta $causaPosta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCausaPostaRequest $request, CausaPosta $causaPosta)
    {
        $causaPosta->update($request->only([
            'nombre',
            'numero_posta',
            'copia_nombre_plantilla',
            'tiene_informe',
            'causa_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$causaPosta
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CausaPosta $causaPosta)
    {
        $causaPosta->es_eliminado   =1;
         $causaPosta->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$causaPosta
        ];
        return response()->json($data);
    }
}
