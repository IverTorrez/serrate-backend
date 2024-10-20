<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\DevolucionSaldo;
use App\Http\Resources\DevolucionSaldoCollection;
use App\Http\Requests\StoreDevolucionSaldoRequest;

class DevolucionSaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devolucionSaldo = DevolucionSaldo::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new DevolucionSaldoCollection($devolucionSaldo);
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
    public function store(StoreDevolucionSaldoRequest $request)
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        $deposito=DevolucionSaldo::create([
            'fecha_devolucion'=>$fechaHora,
            'detalle_devolucion'=>$request->detalle_devolucion,
            'monto'=>$request->monto,
            'causa_id'=>$request->causa_id,
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
         $data=[
            'message'=> MessageHttp::CREADO_CORRECTAMENTE,
            'data'=>$deposito
         ];
         return response()
               ->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(DevolucionSaldo $devolucionSaldo)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$devolucionSaldo
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DevolucionSaldo $devolucionSaldo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DevolucionSaldo $devolucionSaldo)
    {
        $devolucionSaldo->update($request->only([
            'detalle_devolucion',
            'monto',
            'causa_id',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
        'data'=>$devolucionSaldo
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DevolucionSaldo $devolucionSaldo)
    {
        $devolucionSaldo->es_eliminado   =1;
         $devolucionSaldo->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$devolucionSaldo
        ];
        return response()->json($data);
    }
}
