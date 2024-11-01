<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Requests\CostoJudicialVentaRequest;
use App\Models\FinalCosto;
use App\Services\FinalCostoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinalCostoController extends Controller
{
    protected $finalCostoService;

    public function __construct(FinalCostoService $finalCostoService)
    {
        $this->finalCostoService = $finalCostoService;
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
    public function show(FinalCosto $finalCosto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinalCosto $finalCosto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinalCosto $finalCosto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinalCosto $finalCosto)
    {
        //
    }
    public function colocarCostoJudicialVenta(CostoJudicialVentaRequest $request, FinalCosto $finalCosto)
    {
        if ($finalCosto->es_validado === 1) {
            return response()->json([
                'message' => 'Error, esta orden ya se coloco el costo judicial venta.',
                'data' => null
            ], 409);
        }
        DB::beginTransaction();
        try {
            $costoProcesalVenta = $request->costo_procesal_venta;
            $totalEgreso = $costoProcesalVenta + $finalCosto->costo_procuraduria_venta;
            $gananciaProcesal = $costoProcesalVenta - $finalCosto->costo_procesal_compra;
            $dataFinalCosto = [
                'costo_procesal_venta' => $costoProcesalVenta,
                'total_egreso' => $totalEgreso,
                'ganancia_procesal' => $gananciaProcesal,
                'es_validado' => 1
            ];

            $finalCosto = $this->finalCostoService->update($dataFinalCosto, $finalCosto->id);
            DB::commit();
            return response()->json([
                'message' => 'Costo Judicial venta registrado correctamente',
                'data' => $finalCosto
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al colocar costo judicia venta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al colocar costo judicia venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
