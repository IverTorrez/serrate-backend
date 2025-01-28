<?php

namespace App\Services;

use App\Constants\Estado;
use App\Constants\EstadoCausa;
use App\Constants\EtapaOrden;
use App\Models\Causa;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CausaService
{
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

            'estado' => EstadoCausa::CONGELADA,
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
}
