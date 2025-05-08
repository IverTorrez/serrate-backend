<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\EstadoCausa;
use App\Constants\EtapaOrden;
use App\Models\Causa;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\BilleteraService;

class CausaService
{
    protected $billeteraService;

    public function __construct(BilleteraService $billeteraService)
    {
        $this->billeteraService = $billeteraService;
    }
    public function store($data)
    {
        $causa = Causa::create([
            'nombre' => $data['nombre'],
            'observacion' => $data['observacion'],
            'objetivos' => $data['objetivos'],
            'estrategia' => $data['estrategia'],
            'informacion' => $data['informacion'],
            'apuntes_juridicos' => $data['apuntes_juridicos'],
            'apuntes_honorarios' => $data['apuntes_honorarios'],
            'tiene_billetera' => $data['tiene_billetera'],
            'billetera' => $data['billetera'],
            'saldo_devuelto' => $data['saldo_devuelto'],
            'color' => $data['color'],
            'materia_id' => $data['materia_id'],
            'tipolegal_id' => $data['tipolegal_id'],
            'categoria_id' => $data['categoria_id'],
            'abogado_id' => $data['abogado_id'],
            'procurador_id' => $data['procurador_id'],
            'usuario_id' => $data['usuario_id'],
            'plantilla_id' => $data['plantilla_id'],

            'estado' => EstadoCausa::ACTIVA,
            'motivo_congelada' => $data['motivo_congelada'],
            'es_eliminado' => 0
        ]);
        return $causa;
    }
    public function update($data, $causaId)
    {
        $causa = Causa::findOrFail($causaId);
        $causa->update($data);
        return $causa;
    }
    public function obtenerUno($causaId)
    {
        $causa = Causa::findOrFail($causaId);
        $causa->load('materia');
        $causa->load('tipoLegal');
        $causa->load('categoria');
        $causa->load('abogado.persona');
        $causa->load('procurador.persona');
        return $causa;
    }
    public function listarActivos()
    {
        $causas = Causa::where('estado', EstadoCausa::ACTIVA)
            ->where('es_eliminado', 0)
            ->get();
        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return $causas;
    }
    public function listarCausasParaPaquete()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('estado', EstadoCausa::CONGELADA)
            ->where('es_eliminado', 0)
            ->where('usuario_id', $usuarioId)
            ->with([
                'materia',
                'tipoLegal'
            ])
            ->whereDoesntHave('paqueteCausas', function ($query) {
                // Verificar que no tengan registros activos en paquete_causas
                $query->where('estado', Estado::ACTIVO)
                    ->where('es_eliminado', 0);
            })
            ->get();
        return $causas;
    }
    public function tieneOrdenesNoCerradas($causaId): bool
    {
        return Orden::where('causa_id', $causaId)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('etapa_orden', '!=', EtapaOrden::CERRADA)
            ->exists();
    }
    public function cuasaNoEstaActiva($causaId): bool
    {
        return Causa::where('id', $causaId)
            ->whereIn('estado', [EstadoCausa::CONGELADA, EstadoCausa::TERMINADA])
            ->where('es_eliminado', 0)
            ->exists();
    }
    public function listarCausasConBilletera()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('tiene_billetera', 1)
            ->where('usuario_id', $usuarioId)
            ->with([
                'materia',
                'tipoLegal'
            ])
            ->get();
        return $causas;
    }
    public function listarCausasDestinoTransaccion()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('tiene_billetera', 1)
            ->where('usuario_id', $usuarioId)
            ->with([
                'materia',
                'tipoLegal'
            ])
            ->get();
        return $causas;
    }
    public function listadoDetallesCausasConBilleterasDeUsuario()
    {
        $usuarioId = Auth::user()->id;
        $causas = Causa::where('estado', '!=', EstadoCausa::TERMINADA)
            ->where('es_eliminado', 0)
            ->where('tiene_billetera', 1)
            ->where('usuario_id', $usuarioId)
            ->with(['materia', 'tipoLegal', 'primerDemandante', 'primerDemandado', 'primerTribunal'])
            ->get();
        return $causas;
    }
    public function obtenerCodigoIdentificadorVisual($causaId): ?string
    {
        $causa = Causa::with(['materia', 'tipoLegal'])->find($causaId);

        if (!$causa || !$causa->materia || !$causa->tipoLegal) {
            return null;
        }
        $codigo = $causa->materia->abreviatura . '-' . $causa->tipoLegal->abreviatura . '-' . $causa->id;
        return $codigo;
    }
    public function obtenerDineroComprometidoCausa(int $causaId): float
    {
        $causa = Causa::findOrFail($causaId);

        return $causa->getTotalDineroComprometidoOrdenesDeCausa();
    }
    public function obtenerTotalComprometidoSinBilletera($usuarioId): float
    {
        $total = 0;
        // Obtener causas sin billetera
        $causas = Causa::where('tiene_billetera', 0)
            ->where('usuario_id', $usuarioId)
            ->where('es_eliminado', 0)->with([
                'ordenes' => function ($query) {
                    $query->where('etapa_orden', '!=', EtapaOrden::CERRADA)
                        ->where('estado', Estado::ACTIVO)
                        ->where('es_eliminado', 0);
                },
                'ordenes.cotizacion',
                'ordenes.presupuesto',
                'ordenes.descarga',
            ])->get();

        foreach ($causas as $causa) {
            foreach ($causa->ordenes as $orden) {
                $venta = $orden->cotizacion->venta ?? 0;
                $monto = $orden->presupuesto->monto ?? 0;
                $saldoDescarga = $orden->descarga->saldo ?? 0;
                $saldoDescargaFormateado = $saldoDescarga !== 0 ? $saldoDescarga * -1 : 0;
                $total += $venta + $monto + $saldoDescargaFormateado;
            }
        }

        return $total;
    }

    public function noPasoValidacionEAPECausa($causaId, $montoProbable): bool
    {
        $causa = Causa::findOrFail($causaId);
        $idUserCausa = $causa->usuario_id;
        $montoTotalProbableComprometido = 0;
        $saldoTotal = 0;
        //Si la causa tiene billetera individual, se hace un calculo de una causa
        if ($causa->tiene_billetera === 1) {
            $montoComprometido = $this->obtenerDineroComprometidoCausa($causaId);
            $montoTotalProbableComprometido = $montoComprometido + $montoProbable;
            $saldoTotal = $causa->billetera;
        } else { //si no tiene billetera, se hace un calculo de las causas sin billeteras y billetera  del usuario
            $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($idUserCausa);
            $montoComprometido = $this->obtenerTotalComprometidoSinBilletera($idUserCausa);
            $montoTotalProbableComprometido = $montoComprometido + $montoProbable;
            $saldoTotal = $billetera->monto;
        }
        return $montoTotalProbableComprometido > $saldoTotal;
    }
    //Funcion eape cuando se hace una transaccion directamente desde la billetera general
    public function noPasoValidacionEAPEBilleteraGral($montoProbable): bool
    {
        $usuarioId = Auth::user()->id;
        //se hace un calculo de las causas sin billeteras y billetera  del usuario
        $billetera = $this->billeteraService->obtenerUnoPorAbogadoId($usuarioId);
        $montoComprometido = $this->obtenerTotalComprometidoSinBilletera($usuarioId);
        $montoTotalProbableComprometido = $montoComprometido + $montoProbable;
        $saldoTotal = $billetera->monto;

        return $montoTotalProbableComprometido > $saldoTotal;
    }
}
