<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UserController;
use App\Http\Requests\StoreCausaRequest;
use App\Http\Resources\CausaCollection;
use App\Models\Causa;
use Illuminate\Http\Request;
use App\Constants\EstadoCausa;
use App\Http\Requests\UpdateCausaRequest;
use Illuminate\Support\Facades\Auth;

class CausaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $causa = Causa::where('es_eliminado', 0)
                           ->paginate();
        return new CausaCollection($causa);
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
    public function store(StoreCausaRequest $request)
    {
        $userController = new UserController();
        $usuarioPmaestro = $userController->obtenerUnPMaestro();
        //$estado=Estado::ACTIVO;
        $idUser=Auth::user()->id;
        $causa=Causa::create([
            'nombre'=>$request->nombre,
            'observacion'=>$request->observacion,
            'objetivos'=>$request->objetivos,
            'estrategia'=>$request->estrategia,
            'informacion'=>$request->informacion,
            'apuntes_juridicos'=>$request->apuntes_juridicos,
            'apuntes_honorarios'=>$request->apuntes_honorarios,
            'tiene_billetera'=>$request->tiene_billetera,
            'billetera'=>0,
            'saldo_devuelto'=>0,
            'color'=>$request->color,
            'materia_id'=>$request->materia_id,
            'tipolegal_id'=>$request->tipolegal_id,
            'categoria_id'=>$request->categoria_id,
            'abogado_id'=>$idUser,
            'procurador_id'=>$usuarioPmaestro->id,
            'usuario_id'=>$idUser,
            'estado'=>EstadoCausa::ACTIVA,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$causa
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Causa $causa)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$causa
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Causa $causa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCausaRequest $request, Causa $causa)
    {
        $causa->update($request->only([
            'nombre',
            'observacion',
            'objetivos',
            'estrategia',
            'informacion',
            'apuntes_juridicos',
            'apuntes_honorarios',
           // 'tiene_billetera',
            'color',
            'materia_id',
            'tipolegal_id',
            'categoria_id',
            'abogado_id',
            'procurador_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$causa
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Causa $causa)
    {
        $causa->es_eliminado =1;
         $causa->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$causa
        ];
        return response()->json($data);
    }
}
