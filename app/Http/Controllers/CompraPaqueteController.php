<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraPaqueteRequest;
use App\Models\CompraPaquete;
use App\Services\CompraPaqueteService;
use App\Services\PaqueteService;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Enums\MessageHttp;
use App\Http\Resources\CompraPaqueteCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CompraPaqueteController extends Controller
{
    protected $compraPaqueteService;
    protected $paqueteService;

    public function __construct(
        CompraPaqueteService $compraPaqueteService,
        PaqueteService $paqueteService
    ) {
        $this->compraPaqueteService = $compraPaqueteService;
        $this->paqueteService = $paqueteService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CompraPaquete::active();
        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }
        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }
        $perPage = $request->input('perPage', 10);
        $compraPaquetes = $query->paginate($perPage);

        return new CompraPaqueteCollection($compraPaquetes);
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
    public function store(StoreCompraPaqueteRequest $request)
    {
        DB::beginTransaction();
        try {
            $paquete = $this->paqueteService->obtenerUno($request->paquete_id);
            $fechaHora = Carbon::now('America/La_Paz')->toDateTimeString();
            $fechaInicioVigencia = Carbon::now();
            $fechaFinalVigencia = $fechaInicioVigencia->copy()->addMonths($paquete->cantidad_mes);
            $cantidadDias = $fechaInicioVigencia->diffInDays($fechaFinalVigencia);
            $data = [
                'monto' => $request->monto,
                'fecha_ini_vigencia' => $fechaInicioVigencia->format('Y-m-d'),
                'fecha_fin_vigencia' => $fechaFinalVigencia->format('Y-m-d'),
                'fecha_compra' => $fechaHora,
                'cantidad_causas' => $paquete->cantidad_causas,
                'dias_vigente' => $cantidadDias,
                'paquete_id' => $request->paquete_id,
                'usuario_id' => Auth::user()->id,
            ];
            $compraPaquete = $this->compraPaqueteService->store($data);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $compraPaquete
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear la orden: ' . $e->getMessage());

            return response()->json([
                'message' => MessageHttp::ERROR_CREAR,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CompraPaquete $compraPaquete)
    {
        $compraPaquete = $this->compraPaqueteService->obtenerUno($compraPaquete->id);
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $compraPaquete
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompraPaquete $compraPaquete)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompraPaquete $compraPaquete)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompraPaquete $compraPaquete)
    {
        //
    }
    public function listarActivosPorUsuario()
    {
        $compraPaquetes = $this->compraPaqueteService->listarActivosPorUsuario();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $compraPaquetes
        ];
        return response()->json($data);
    }
}
