<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\TransaccionesCausa;
use App\Models\Video;
use Illuminate\Http\Request;

class TransaccionesCausaService
{
    public function store($data)
    {
        $transaccionesCausa = TransaccionesCausa::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'transaccion' => $data['transaccion'],
            'glosa' => $data['glosa'],
            'causa_id' => $data['causa_id'],
            'causa_origen_destino' => $data['causa_origen_destino'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $transaccionesCausa;
    }
    public function update($data, $transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        $transaccionesCausa->update($data);
        return $transaccionesCausa;
    }
    public function listarActivos()
    {
        $transaccionesCausa = TransaccionesCausa::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $transaccionesCausa;
    }
    public function destroy($transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        $transaccionesCausa->es_eliminado = 1;
        $transaccionesCausa->save();
        return $transaccionesCausa;
    }
    public function obtenerUno($transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        return $transaccionesCausa;
    }

}
