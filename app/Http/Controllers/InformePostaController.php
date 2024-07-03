<?php

namespace App\Http\Controllers;

use App\Models\InformePosta;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\StoreInformePostaRequest;
use App\Http\Requests\UpdateInformePostaRequest;
use App\Http\Resources\InformePostaCollection;
use Carbon\Carbon;

class InformePostaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $informePosta = InformePosta::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new InformePostaCollection($informePosta);
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
    public function store(StoreInformePostaRequest $request)
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();

        $materia=InformePosta::create([
            'foja_informe'=>$request->foja_informe,
            'fecha_informe'=>$request->fecha_informe,
            'calculo_gasto'=>$request->calculo_gasto,
            'honorario_informe'=>$request->honorario_informe,

            'fecha_truncamiento'=>$fechaHora,
            'esta_escrito'=>0,

            'foja_truncamiento'=>$request->foja_truncamiento,
            'honorario_informe_truncamiento'=>$request->honorario_informe_truncamiento,
            'tipoposta_id'=>$request->tipoposta_id,
            'causaposta_id'=>$request->causaposta_id,
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);

         $data=[
            'message'=>'Registro creado exitosamente',
            'data'=>$materia
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(InformePosta $informePosta)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$informePosta
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InformePosta $informePosta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInformePostaRequest $request, InformePosta $informePosta)
    {
        $informePosta->update($request->only([
            'foja_informe',
            'fecha_informe',
            'calculo_gasto',
            'honorario_informe',
            'foja_truncamiento',
            'fecha_truncamiento',
            'honorario_informe_truncamiento',
            'esta_escrito',
            'tipoposta_id',
            'causaposta_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$informePosta
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InformePosta $informePosta)
    {
        $informePosta->es_eliminado   =1;
         $informePosta->save();
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$informePosta
        ];
        return response()->json($data);
    }
}
