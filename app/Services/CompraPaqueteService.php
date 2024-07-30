<?php
namespace App\Services;

use App\Models\Orden;
use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;
use App\Models\CompraPaquete;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CompraPaqueteService
{
    public function index()
    {
        $compraPaquete = CompraPaquete::where('es_eliminado', 0)
                                        ->where('estado', Estado::ACTIVO)
                                        ->paginate();
        return $compraPaquete;
    }

    public function store($data)
    {
        $compraPaquete = CompraPaquete::create([
            'monto' => $data['monto'],
            'fecha_ini_vigencia' => $data['fecha_ini_vigencia'],
            'fecha_fin_vigencia' => $data['fecha_fin_vigencia'],
            'fecha_compra' => $data['fecha_compra'],
            'cantidad_causas' => $data['cantidad_causas'],
            'dias_vigente' => $data['dias_vigente'],
            'paquete_id' => $data['paquete_id'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $compraPaquete;
    }
    public function update($data, $compraId)
    {
        $compraPaquete = CompraPaquete::findOrFail($compraId);
        $compraPaquete->update($data);
        return $compraPaquete;
    }
    public function obtenerUno($compraId)
    {
        $compraPaquete = CompraPaquete::find($compraId);
        if (!$compraPaquete) {
            throw new ModelNotFoundException('La compra con ID ' . $compraId . ' no existe.');
        }
        return $compraPaquete;
    }


}
