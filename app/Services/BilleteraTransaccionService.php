<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\BilleteraTransaccion;
use Illuminate\Http\Request;

class BilleteraTransaccionService
{
    public function store($data)
    {
        $billeteraTransaccion = BilleteraTransaccion::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'billetera_id' => $data['billetera_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $billeteraTransaccion;
    }
    public function update($data, $transaccionId)
    {
        $billeteraTransaccion = BilleteraTransaccion::findOrFail($transaccionId);
        $billeteraTransaccion->update($data);
        return $billeteraTransaccion;
    }
    public function obtenerUno($transaccionId)
    {
        $billeteraTransaccion = BilleteraTransaccion::findOrFail($transaccionId);
        return $billeteraTransaccion;
    }
    public function listarActivos()
    {
        $billeteraTransaccion = BilleteraTransaccion::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $billeteraTransaccion;
    }
    public function destroy($transaccionId)
    {
        $billeteraTransaccion = BilleteraTransaccion::findOrFail($transaccionId);
        $billeteraTransaccion->es_eliminado = 1;
        $billeteraTransaccion->save();
        return $billeteraTransaccion;
    }
}
