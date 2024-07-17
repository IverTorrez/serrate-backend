<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePresupuestoRequest;
use App\Http\Resources\PresupuestoCollection;
use App\Models\Presupuesto;
use Illuminate\Http\Request;
use App\Services\PresupuestoService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Constants\Estado;
use App\Constants\EtapaOrden;
use App\Http\Requests\UpdatePresupuestoRequest;
use App\Services\CotizacionService;
use App\Services\MatrizCotizacionService;
use App\Services\OrdenService;

class PresupuestoController extends Controller
{
    protected $presupuestoService;
    protected $ordenService;
    protected $matrizCotizacionService;
    protected $cotizacionService;
    public function __construct(PresupuestoService $presupuestoService,
                                OrdenService $ordenService,
                                MatrizCotizacionService $matrizCotizacionService,
                                CotizacionService $cotizacionService
                                )
    {
        $this->presupuestoService = $presupuestoService;
        $this->ordenService = $ordenService;
        $this->matrizCotizacionService = $matrizCotizacionService;
        $this->cotizacionService = $cotizacionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presupuesto = Presupuesto::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new PresupuestoCollection($presupuesto);
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
    public function store(StorePresupuestoRequest $request)
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        $data = [
            'monto' => $request->monto,
            'detalle_presupuesto' => $request->detalle_presupuesto,
            'fecha_presupuesto' => $fechaHora,
            'fecha_entrega' => null,
            'contador_id' => Auth::user()->id,
            'orden_id' => $request->orden_id,
        ];
        $presupuesto = $this->presupuestoService->store($data);

        //MARCA LA ORDEN PRESUPUESTADA
        $cotizacion= $this->cotizacionService->obtenerPorIdOrden($presupuesto->orden_id);
        $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($request->prioridad, $cotizacion->condicion);

        $dataOrden = [
            'prioridad' => $request->prioridad,
            'procurador_id' => $request->procurador_id,
            'etapa_orden'=>EtapaOrden::PRESUPUESTADA,
            'matriz_id'=>$matrizCotizacion->id,
        ];
        $orden= $this->ordenService->update($dataOrden,$presupuesto->orden_id);

        //ACTUALIZACION DE COTIZACION
        $dataCotizacion = [
            'compra' => $matrizCotizacion->precio_compra,
            'venta' => $matrizCotizacion->precio_venta,
            'penalizacion'=> $matrizCotizacion->penalizacion,
            'prioridad'=> $matrizCotizacion->numero_prioridad
        ];
        $cotizacion= $this->cotizacionService->update($dataCotizacion,$cotizacion->id);

        return response()->json([
            'message' => 'Registro creado correctamente',
            'data' => $presupuesto
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Presupuesto $presupuesto)
    {
        $data=[
            'message'=>'Resultado obtenido exitosamente',
            'data'=>$presupuesto
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presupuesto $presupuesto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresupuestoRequest $request, Presupuesto $presupuesto)
    {
        $data = $request->only([
            'monto',
            'detalle_presupuesto',
            'orden_id',
            'prioridad',
            'procurador_id',
        ]);
        $presupuesto=$this->presupuestoService->update($data,$presupuesto->id);

        if($request->prioridad){
            //ACTUALIZACION DE ORDENS
            $cotizacion= $this->cotizacionService->obtenerPorIdOrden($presupuesto->orden_id);
            $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($request->prioridad, $cotizacion->condicion);
            $dataOrden = [
                'prioridad' => $request->prioridad,
                'procurador_id' => $request->procurador_id,
                'matriz_id'=>$matrizCotizacion->id,
            ];
            $orden= $this->ordenService->update($dataOrden,$presupuesto->orden_id);

            //ACTUALIZACION DE COTIZACION
            $dataCotizacion = [
                'compra' => $matrizCotizacion->precio_compra,
                'venta' => $matrizCotizacion->precio_venta,
                'penalizacion'=> $matrizCotizacion->penalizacion,
                'prioridad'=> $matrizCotizacion->numero_prioridad
            ];
            $cotizacion= $this->cotizacionService->update($dataCotizacion,$cotizacion->id);
        }

        $data=[
            'message'=>'Registro actualizado correctamente',
            'data'=>$presupuesto
            ];
            return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presupuesto $presupuesto)
    {
        $presupuesto = $this->presupuestoService->destroy($presupuesto);
         $data=[
            'message'=>'Registro eliminado correctamente',
            'data'=>$presupuesto
        ];
        return response()->json($data);
    }
    public function entregarPresupuesto(Presupuesto $presupuesto)
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        $data=[
            'fecha_entrega'=>$fechaHora
        ];
        $presupuesto=$this->presupuestoService->update($data,$presupuesto->id);

       //ACTUALIZA ETAPA DE LA ORDEN
        $dataOrden=[
            'etapa_orden'=>EtapaOrden::DINERO_ENTREGADO,
        ];
        $orden=$this->ordenService->update($dataOrden,$presupuesto->orden_id);

        $data=[
            'message'=>'Dinero entregado correctamente',
            'data'=>$presupuesto
        ];
        return response()->json($data);

    }
}
