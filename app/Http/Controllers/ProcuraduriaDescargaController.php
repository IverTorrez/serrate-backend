<?php

namespace App\Http\Controllers;

use App\Models\ProcuraduriaDescarga;
use Illuminate\Http\Request;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Http\Requests\StoreProcuraduriaDescargaRequest;
use App\Http\Resources\ProcuraduriaDescargaCollection;
use App\Services\ConfirmacionService;
use App\Services\OrdenService;
use App\Services\PresupuestoService;
use App\Services\ProcuraduriaDescargaService;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcuraduriaDescargaController extends Controller
{
    protected $procuraduriaDescargaService;
    protected $presupuestoService;
    protected $ordenService;
    protected $confirmacionService;

    public function __construct(
                                 ProcuraduriaDescargaService $procuraduriaDescargaService,
                                 PresupuestoService $presupuestoService,
                                 OrdenService $ordenService,
                                 ConfirmacionService $confirmacionService
                                )
    {
        $this->procuraduriaDescargaService = $procuraduriaDescargaService;
        $this->presupuestoService = $presupuestoService;
        $this->ordenService = $ordenService;
        $this->confirmacionService = $confirmacionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $descarga = $this->procuraduriaDescargaService->index();
        return new ProcuraduriaDescargaCollection($descarga);
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
    public function store(StoreProcuraduriaDescargaRequest $request)
    {
        DB::beginTransaction();
        try{
            $now=Carbon::now('America/La_Paz');
            $fechaHora=$now->toDateTimeString();

            $presupuesto = $this->presupuestoService->obtenerUnoPorOrdenId($request->orden_id);
            $saldo=$presupuesto->monto - $request->gastos;

            $data=[
                'detalle_informacion' => $request->detalle_informacion,
                'detalle_documentacion' => $request->detalle_documentacion,
                'ultima_foja' => $request->ultima_foja,
                'gastos' => $request->gastos,
                'saldo' => $saldo,
                'detalle_gasto' => $request->detalle_gasto,
                'fecha_descarga' => $fechaHora,
                'compra_judicial' => $request->compra_judicial,
                'orden_id' => $request->orden_id,
            ];

            $descarga= $this->procuraduriaDescargaService->store($data);

            //ACTUALIZA LA ETAPA DE LA ORDEN
            $dataOrden=[
                'etapa_orden'=>EtapaOrden::DESCARGADA
            ];
            $orden = $this->ordenService->update($dataOrden,$request->orden_id);
            //INSERTA LA CONFIRMACION DEL SISTEMA
            $confirmacionSistema = $descarga->fecha_descarga <= $orden->fecha_fin ? 1 : 0;

            $dataConfirmacion = [
                'confir_sistema' => $confirmacionSistema,
                'descarga_id' => $descarga->id
            ];
            $confirmacion = $this->confirmacionService->store($dataConfirmacion);

            DB::commit();
            return response()->json([
                'message' => 'Registro creado correctamente',
                'data' => $descarga
            ], 201);

       }catch (Exception $e) {
        DB::rollBack();
        Log::error('Error registrar descarga: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error registrar descarga',
            'error' => $e->getMessage()
        ], 500);
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcuraduriaDescarga $procuraduriaDescarga)
    {
        //
    }
}
