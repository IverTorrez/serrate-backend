<?php

namespace App\Http\Controllers;

use App\Constants\GlosaTransaccion;
use Exception;
use Carbon\Carbon;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Constants\TipoTransaccion;
use App\Constants\TransaccionCausa;
use App\Models\TransaccionesCausa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\TransaccionesCausaService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTransaccionesCausaRequest;
use App\Services\BilleteraService;
use App\Services\BilleteraTransaccionService;
use App\Services\CausaService;

class TransaccionesCausaController extends Controller
{
    protected $transaccionesCausaService;
    protected $causaService;
    protected $billeteraTransaccionService;
    protected $billeteraService;

    public function __construct(TransaccionesCausaService $transaccionesCausaService, CausaService $causaService, BilleteraTransaccionService $billeteraTransaccionService, BilleteraService $billeteraService)
    {
        $this->transaccionesCausaService = $transaccionesCausaService;
        $this->causaService = $causaService;
        $this->billeteraTransaccionService = $billeteraTransaccionService;
        $this->billeteraService = $billeteraService;
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
    public function store(StoreTransaccionesCausaRequest $request)
    {
        $monto = $request->monto;
        $esTransaccionDesdeBilletera = $request->desde_billetera;
        $causaId = $request->causa_id;
        $esTransferenciaEntreCausas = 0;
        $usuarioId = Auth::user()->id;
        $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($usuarioId);
        //Validacion cuando es transaccion desde billeta
        if ($esTransaccionDesdeBilletera === 1 && $billetera->monto < $monto) {
            return response()->json([
                'message' => 'No tiene suficiente saldo en su billetera.',
                'data' => null
            ], 409);
        }
        //Validacion cuando es transaccion entre causas
        if($esTransaccionDesdeBilletera === 0)
        {
            if($request->causa_origen_destino === $causaId){
                return response()->json([
                    'message' => 'No puede hacer transferencias entre la misma causa,',
                    'data' => null
                ], 409);
            }
            //Valida el saldo de la causa origen
            $causaOrigenSaldo = $this->causaService->obtenerUno($request->causa_origen_destino);
            if($causaOrigenSaldo->billetera < $monto){
                return response()->json([
                    'message' => 'Esta causa no tiene suficiente saldo en su billetera para hacer transferencias.',
                    'data' => null
                ], 409);
            }
        }
        DB::beginTransaction();
        try {
            if ($esTransaccionDesdeBilletera === 1) {
                $glosaDestino = GlosaTransaccion::CREDITO_DESDE_BILLETERA;
                $transaccionDestino = TransaccionCausa::DEPOSITO;
                $causa_origen_destino = 0;
                //Actualizacion de saldo de billetera
                $tipoTrn = TipoTransaccion::DEBITO;
                $glosabilletera = GlosaTransaccion::DEBITO_DESDE_BILLETERA_POR_TRANSFERENCIA_A_CAUSA;
                $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($usuarioId);
                $billeterTransaccion = $this->billeteraTransaccionService->reistroTransaccionBilletera($billetera->id, $monto, $tipoTrn, $glosabilletera);
            } else {
                $glosaDestino = GlosaTransaccion::CREDITO_DESDE_CAUSA;
                $transaccionDestino = TransaccionCausa::TRANSFERENCIA_RECIBO;
                $causa_origen_destino = $request->causa_origen_destino;
                //Para el registro de causa origin transaccion
                $transaccionOrigen = TransaccionCausa::TRANSFERENCIA_ENVIO;
                $glosaOrigen = GlosaTransaccion::DEBITO_POR_TRASPASO;
                $esTransferenciaEntreCausas = 1;
            }

            $now = Carbon::now('America/La_Paz');
            $fechaHora = $now->toDateTimeString();
            $dataDestino = [
                'monto' => $monto,
                'fecha_transaccion' => $fechaHora,
                'tipo' => TipoTransaccion::CREDITO,
                'transaccion' => $transaccionDestino,
                'glosa' => $glosaDestino,
                'causa_id' => $causaId,
                'causa_origen_destino' => $causa_origen_destino,
                'usuario_id' => $usuarioId
            ];
            $transaccionCausaDestino = $this->transaccionesCausaService->store($dataDestino);
            //Actualiza saldo de la causa destino
            $causaDestino = $this->causaService->obtenerUno($causaId);
            $saldoCausaDestino = $causaDestino->billetera + $monto;
            $dataCausaDestino = [
                'billetera' => $saldoCausaDestino
            ];
            $causaDest = $this->causaService->update($dataCausaDestino, $causaId);

            if ($esTransferenciaEntreCausas === 1) {
                $dataOrigen = [
                    'monto' => $monto,
                    'fecha_transaccion' => $fechaHora,
                    'tipo' => TipoTransaccion::DEBITO,
                    'transaccion' => $transaccionOrigen,
                    'glosa' => $glosaOrigen,
                    'causa_id' => $causa_origen_destino,
                    'causa_origen_destino' => $causaId,
                    'usuario_id' => Auth::user()->id
                ];
                $transaccionCausaOrigen = $this->transaccionesCausaService->store($dataOrigen);
                //Actualiza saldo de la causa origen
                $causaOrigen = $this->causaService->obtenerUno($causa_origen_destino);
                $saldoCausaOrigen = $causaOrigen->billetera - $monto;
                $dataCausaOrigen = [
                    'billetera' => $saldoCausaOrigen
                ];
                $causaOrig = $this->causaService->update($dataCausaOrigen, $causa_origen_destino);
            }

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $transaccionCausaDestino
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar transaccion en causa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error interno al registrar transaccion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TransaccionesCausa $transaccionesCausa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransaccionesCausa $transaccionesCausa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransaccionesCausa $transaccionesCausa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransaccionesCausa $transaccionesCausa)
    {
        //
    }
}
