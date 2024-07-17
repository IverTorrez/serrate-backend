<?php

namespace App\Http\Controllers;

use App\Models\MatrizCotizacion;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Http\Requests\UpdateMatrizCotizacionRequest;
use App\Http\Resources\MatrizCotizacionCollection;
use App\Services\MatrizCotizacionService;

class MatrizCotizacionController extends Controller
{
    protected $matrizCotizacionService;

    public function __construct(MatrizCotizacionService $matrizCotizacionService)
    {
        $this->matrizCotizacionService = $matrizCotizacionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matrizCotizacion = MatrizCotizacion::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new MatrizCotizacionCollection($matrizCotizacion);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MatrizCotizacion $matrizCotizacion)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$matrizCotizacion
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MatrizCotizacion $matrizCotizacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMatrizCotizacionRequest $request, MatrizCotizacion $matrizCotizacion)
    {
        $matrizCotizacion->update($request->only([
            'numero_prioridad',
            'precio_compra',
            'precio_venta',
            'penalizacion',
            'condicion',
            'estado',
            'es_eliminado']));
        $data=[
        'message'=>'Registro actualizado correctamente',
        'data'=>$matrizCotizacion
        ];
        return response()->json($data);
    }
    public function obtenerIdDePrioridadYCondicion($prioridad,$condicion){

        $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($prioridad, $condicion);

        $data = [
            'message' => 'Registro obtenido correctamente',
            'data' => $matrizCotizacion
        ];

        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MatrizCotizacion $matrizCotizacion)
    {
        //
    }
}
