<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConfirmacionRequest;
use App\Models\Confirmacion;
use App\Services\ConfirmacionService;
use App\Services\OrdenService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Constants\EtapaOrden;
use App\Services\CotizacionService;
use App\Services\FinalCostoService;
use App\Services\ProcuraduriaDescargaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConfirmacionController extends Controller
{
    protected $confirmacionService;
    protected $ordenService;
    protected $procuraduriaDescargaService;
    protected $finalCostoService;
    protected $cotizacionService;


    public function __construct(
        ConfirmacionService $confirmacionService,
        OrdenService $ordenService,
        ProcuraduriaDescargaService $procuraduriaDescargaService,
        FinalCostoService $finalCostoService,
        CotizacionService $cotizacionService
    )
    {
        $this->confirmacionService = $confirmacionService;
        $this->ordenService = $ordenService;
        $this->procuraduriaDescargaService = $procuraduriaDescargaService;
        $this->finalCostoService = $finalCostoService;
        $this->cotizacionService = $cotizacionService;
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Confirmacion $confirmacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Confirmacion $confirmacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Confirmacion $confirmacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Confirmacion $confirmacion)
    {
        //
    }
    public function pronuncioAbogado(UpdateConfirmacionRequest $request, Confirmacion $confirmacion)
    {
        DB::beginTransaction();
        try{
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $data= $request->only([
            'confir_abogado',
            'confir_contador',
            'justificacion_rechazo'
        ]);

        if ($request->has('justificacion_rechazo') && $request->justificacion_rechazo === '') {
            $data['justificacion_rechazo'] = '';
        }
        $data['fecha_confir_abogado'] = $fechaHora;
        $confirmacion = $this->confirmacionService->update($data,$confirmacion->id);

        $descarga = $this->procuraduriaDescargaService->obtenerUno($confirmacion->descarga_id);
        if ($confirmacion->fecha_confir_contador === NULL){
            //ACTUALIZA LA ETAPA DE LA ORDEN CON PRONUNCIAMIENTO DEL ABOGADO
            $dataOrden=[
                'etapa_orden'=>EtapaOrden::PRONUNCIO_ABOGADO
            ];
            $orden = $this->ordenService->update($dataOrden,$descarga->orden_id);
        }else{
            //CIERRE DE LA ORDEN
            $calificacionOrden = ($confirmacion->confir_abogado === 1 && $confirmacion->confir_sistema === 1) ? 1 : 0;
            $ordenCerrada = $this->cerrarOrden($calificacionOrden,$descarga->orden_id);
        }


        DB::commit();
        return response()->json([
            'message' => 'Registro actualizado correctamente',
            'data' => $confirmacion
        ], 200);

       }catch (Exception $e) {
        DB::rollBack();
        Log::error('Error pronuncio abogado: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error pronuncio abogado',
            'error' => $e->getMessage()
        ], 500);
       }
    }

    public function pronuncioContador(UpdateConfirmacionRequest $request, Confirmacion $confirmacion)
    {
        DB::beginTransaction();
        try{
            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            $data= $request->only([
                'confir_contador',
            ]);
            $data['fecha_confir_contador'] = $fechaHora;
            $confirmacion = $this->confirmacionService->update($data,$confirmacion->id);
            //VALIDA EL CONTADOR
            $dataDescarga = [
                'es_validado' => 1
            ];
            $descarga = $this->procuraduriaDescargaService->update($dataDescarga, $confirmacion->descarga_id);
            if ($confirmacion->fecha_confir_abogado === NULL){
                //ACTUALIZA LA ETAPA DE LA ORDEN CON PRONUNCIAMIENTO DEL CONTADOR
                $dataOrden=[
                    'etapa_orden'=>EtapaOrden::PRONUNCIO_CONTADOR
                ];
                $orden = $this->ordenService->update($dataOrden,$descarga->orden_id);
            }else{
                //CIERRE DE LA ORDEN
                $calificacionOrden = ($confirmacion->confir_abogado === 1 && $confirmacion->confir_sistema === 1) ? 1 : 0;
                $ordenCerrada = $this->cerrarOrden($calificacionOrden,$descarga->orden_id);
            }

            DB::commit();
            return response()->json([
                'message' => 'Registro actualizado correctamente',
                'data' => $confirmacion
            ], 200);

        }catch (Exception $e) {
            DB::rollBack();
            Log::error('Error pronuncio contador: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error pronuncio contador',
                'error' => $e->getMessage()
            ], 500);
           }
    }

    public function cerrarOrden($calificacionOrden,$ordenId)
    {
        $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
        $calificacion = $calificacionOrden ===1 ? 'SUFICIENTE': 'INSUFICIENTE';
        $dataOrden=[
            'etapa_orden' => EtapaOrden::CERRADA,
            'calificacion' => $calificacion,
            'fecha_cierre' => $fechaHora
        ];
        $orden = $this->ordenService->update($dataOrden,$ordenId);

        $cotizacion = $this->cotizacionService->obtenerPorIdOrden($ordenId);
        if ($orden->calificacion==='SUFICIENTE'){
            $procuraduriaCompra=$cotizacion->compra;
            $procuraduriaVenta=$cotizacion->venta;
            $penalizacion=0;
        }else{
            $procuraduriaCompra=0;
            $procuraduriaVenta=0;
            $penalizacion=$cotizacion->penalizacion;
        }
        //DATOS DE GASTO PROCESAL EN DESCARGA
        $descarga = $this->procuraduriaDescargaService->obtenerUnoPorOrdenId($ordenId);
        $totalEgreso = $descarga->compra_judicial + $procuraduriaVenta;
        $gananciaProcuraduria = $procuraduriaVenta - $procuraduriaCompra;
        $dataFinalCosto = [
            'costo_procuraduria_compra' => $procuraduriaCompra,
            'costo_procuraduria_venta' => $procuraduriaVenta,
            'costo_procesal_compra' => $descarga->compra_judicial,
            'costo_procesal_venta' => $descarga->compra_judicial,
            'total_egreso' => $totalEgreso,
            'penalidad' => $penalizacion,
            'es_validado' => 0,
            'cancelado_procurador' => 0,
            'ganancia_procuraduria' => $gananciaProcuraduria,
            'ganancia_procesal' => 0,
            'orden_id' => $ordenId,
        ];
        $finalCosto = $this->finalCostoService->store($dataFinalCosto);

        return $orden;

    }
}
