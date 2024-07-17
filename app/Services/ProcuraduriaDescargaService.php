<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\ProcuraduriaDescarga;
use Illuminate\Http\Request;

class ProcuraduriaDescargaService
{
    public function index()
    {
        $descarga = ProcuraduriaDescarga::where('es_eliminado', 0)
                                        ->where('estado', Estado::ACTIVO)
                                        ->paginate();
        return $descarga;
    }
    public function store($data)
    {
        $descarga=ProcuraduriaDescarga::create([
            'detalle_informacion'=>$data['detalle_informacion'],
            'detalle_documentacion'=>$data['detalle_documentacion'],
            'ultima_foja'=>$data['ultima_foja'],
            'gastos'=>$data['gastos'],
            'saldo'=>$data['saldo'],
            'detalle_gasto'=>$data['detalle_gasto'],
            'fecha_descarga'=>$data['fecha_descarga'],
            'compra_judicial'=>$data['compra_judicial'],
            'es_validado'=>0,
            'orden_id'=>$data['orden_id'],
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
        return $descarga;
    }
    public function update($data, $descargaId)
    {
        $descarga = ProcuraduriaDescarga::findOrFail($descargaId);
        $descarga->update($data);
        return $descarga;
    }
    public function obtenerUno($descargaId)
    {
        $descarga = ProcuraduriaDescarga::findOrFail($descargaId);
        return $descarga;
    }
    public function obtenerUnoPorOrdenId($ordenId)
    {
        $descarga = ProcuraduriaDescarga::where('orden_id', $ordenId)
                                        ->where('estado',Estado::ACTIVO)
                                        ->where('es_eliminado',0)
                                        ->first();
        return $descarga;
    }

}
