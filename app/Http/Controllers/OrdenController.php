<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Orden;
use App\Constants\Estado;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Services\OrdenService;
use Illuminate\Support\Facades\DB;
use App\Services\CotizacionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\OrdenCollection;
use App\Http\Requests\StoreOrdenRequest;
use App\Http\Requests\UpdateOrdenRequest;
use App\Services\MatrizCotizacionService;


class OrdenController extends Controller
{
    protected $matrizCotizacionService;
    protected $cotizacionService;
    protected $ordenService;

    public function __construct(MatrizCotizacionService $matrizCotizacionService,
                                CotizacionService $cotizacionService,
                                OrdenService $ordenService
                                )
    {
        $this->matrizCotizacionService = $matrizCotizacionService;
        $this->cotizacionService = $cotizacionService;
        $this->ordenService = $ordenService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orden = Orden::where('es_eliminado', 0)
                           ->where('estado', Estado::ACTIVO)
                           ->paginate();
        return new OrdenCollection($orden);
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
    public function store(StoreOrdenRequest $request)
    {
        DB::beginTransaction();
        try{
            $response = $this->obtenetMatrizCotizacion($request->fecha_inicio, $request->fecha_fin, $request->prioridad);
            $matrizCotizacion = $response['matrizCotizacion'];
            $difference = $response['difference'];
            $now=Carbon::now('America/La_Paz');
            $fechaHora=$now->toDateTimeString();
            $tipo=Auth::user()->tipo;
            $data = [
                'entrega_informacion'=>$request->entrega_informacion,
                'entrega_documentacion'=>$request->entrega_documentacion,
                'fecha_inicio'=>$request->fecha_inicio,
                'fecha_fin'=>$request->fecha_fin,
                'fecha_giro'=>$fechaHora,
                'plazo_hora'=>$difference,
                'fecha_recepcion'=>null,
                'etapa_orden'=>EtapaOrden::GIRADA,
                'prioridad'=>$request->prioridad,
                'girada_por'=>$tipo,
                'fecha_ini_bandera'=>$request->fecha_inicio,
                'notificado'=>0,
                'lugar_ejecucion'=>$request->lugar_ejecucion,
                'sugerencia_presupuesto'=>$request->sugerencia_presupuesto,
                'tiene_propina'=>$request->tiene_propina,
                'propina'=>$request->propina,
                'causa_id'=>$request->causa_id,
                'procurador_id'=>$request->procurador_id,
                'matriz_id'=>$matrizCotizacion->id
            ];

            $orden = $this->ordenService->store($data);

                //Registro de cotizacion
                $dataCotizacion = [
                    'compra' => $matrizCotizacion->precio_compra,
                    'venta' => $matrizCotizacion->precio_venta,
                    'penalizacion' => $matrizCotizacion->penalizacion,
                    'prioridad' => $request->prioridad,
                    'condicion' => $matrizCotizacion->condicion,
                    'orden_id' => $orden->id, // ID de la orden obtenida
                ];
                // Llamar al método store del servicio
            $cotizacion = $this->cotizacionService->store($dataCotizacion);
            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $orden
            ], 200);

        }catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la orden: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al crear la orden',
                'error' => $e->getMessage()
            ], 500);
        }
}

    /**
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        $data=[
            'message'=> MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data'=>$orden
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orden $orden)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrdenRequest $request, Orden $orden)
    {
    DB::beginTransaction();
    try{
            $response = $this->obtenetMatrizCotizacion($request->fecha_inicio, $request->fecha_fin, $request->prioridad);
            $matrizCotizacion = $response['matrizCotizacion'];
            $difference = $response['difference'];
            $data = $request->only([
                'entrega_informacion',
                'entrega_documentacion',
                'fecha_inicio',
                'fecha_fin',
                'prioridad',
                'lugar_ejecucion',
                'tiene_propina',
                'propina',
                'procurador_id'
            ]);
            $data['matriz_id'] = $matrizCotizacion->id;
            $data['plazo_hora'] = $difference;

            $orden=$this->ordenService->update($data,$orden->id);
          //Actualizacion de cotizacion
            $cotizacion=$this->cotizacionService->obtenerPorIdOrden($orden->id);
            $dataCotizacion = [
                'compra' => $matrizCotizacion->precio_compra,
                'venta' => $matrizCotizacion->precio_venta,
                'penalizacion' => $matrizCotizacion->penalizacion,
                'prioridad' => $request->prioridad,
                'condicion' => $matrizCotizacion->condicion
            ];

            $cotizacion = $this->cotizacionService->update($dataCotizacion,$cotizacion->id);

            DB::commit();
            $data=[
                'message'=> MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data'=>$orden
            ];
            return response()->json($data);

       } catch (Exception $e) {
        DB::rollBack();
        Log::error('Error actualizando la orden: ' . $e->getMessage());

        return response()->json([
            'message' => 'Error actualizando la orden',
            'error' => $e->getMessage()
        ], 500);
    }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orden $orden)
    {
        $orden->es_eliminado   =1;
         $orden->save();
         $data=[
            'message'=> MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data'=>$orden
        ];
        return response()->json($data);
    }

    public function aceptarOrden(Orden $orden)
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        $data=[
            'fecha_recepcion'=>$fechaHora,
            'etapa_orden'=>EtapaOrden::ACEPTADA
        ];
        $orden=$this->ordenService->update($data,$orden->id);

        $data=[
            'message'=>'Orden aceptada correctamente',
            'data'=>$orden
        ];
        return response()->json($data);
    }

    public function obtenetMatrizCotizacion($fechaInicio, $fechaFin, $prioridad)
    {
        $carbonFecha1 = Carbon::parse($fechaInicio);
        $carbonFecha2 = Carbon::parse($fechaFin);
        $difference = $carbonFecha1->diffInHours($carbonFecha2);
        $condicion=0;
        if($difference>96){
            $condicion=1;
        }
        if($difference>24 && $difference<=96){
            $condicion=2;
        }
        if($difference>8 && $difference<=24){
            $condicion=3;
        }
        if($difference>3 && $difference<=8){
            $condicion=4;
        }
        if($difference>1 && $difference<=3){
            $condicion=5;
        }
        if($difference<=1){
            $condicion=6;
        }
        $matrizCotizacion = $this->matrizCotizacionService->obtenerIdDePrioridadYCondicion($prioridad, $condicion);
      return [
        'matrizCotizacion' => $matrizCotizacion,
        'difference' => $difference
    ];

    }



}
