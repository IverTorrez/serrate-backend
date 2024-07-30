<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Models\GestionAlternativa;
use App\Services\GestionAlternativaService;
use App\Http\Requests\StoreGestionAlternativaRequest;
use App\Http\Requests\UpdateGestionAlternativaRequest;

class GestionAlternativaController extends Controller
{
    protected $gestionAlternativaService;

    public function __construct(
                                 GestionAlternativaService $gestionAlternativaService
                               )
    {
        $this->gestionAlternativaService = $gestionAlternativaService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreGestionAlternativaRequest $request)
    {
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $data = [
            'solicitud_gestion' => $request->solicitud_gestion,
            'fecha_solicitud' => $fechaHora,
            'detalle_gestion' => '',
            'fecha_respuesta' => null,
            'orden_id' => $request->orden_id,
        ];
        $gestionAlternativa = $this->gestionAlternativaService->store($data);

        return response()->json([
            'message' => MessageHttp::CREADO_CORRECTAMENTE,
            'data' => $gestionAlternativa
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(GestionAlternativa $gestionAlternativa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GestionAlternativa $gestionAlternativa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGestionAlternativaRequest $request, GestionAlternativa $gestionAlternativa)
    {
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $data = $request->only([
            'detalle_gestion'
        ]);
        $data['fecha_respuesta'] = $fechaHora;

        $gestionAlternativa = $this->gestionAlternativaService->update($data,$gestionAlternativa->id);
        $data=[
            'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data'=>$gestionAlternativa
        ];
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GestionAlternativa $gestionAlternativa)
    {
        $gestionAlternativa = $this->gestionAlternativaService->destroy($gestionAlternativa);
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$gestionAlternativa
        ];
        return response()->json($data);
    }
    public function obtenerPorOrdenId($ordenId)
    {
        $gestionAlternativa = $this->gestionAlternativaService->obtenerPorOrdenId($ordenId);
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$gestionAlternativa
        ];
        return response()->json($data);

    }
}
