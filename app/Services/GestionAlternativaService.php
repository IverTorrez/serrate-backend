<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\GestionAlternativa;
use Illuminate\Http\Request;

class GestionAlternativaService
{
    public function store($data)
    {
        $gestionAlternativa=GestionAlternativa::create([
            'solicitud_gestion'=>$data['solicitud_gestion'],
            'fecha_solicitud'=>$data['fecha_solicitud'],
            'detalle_gestion'=>$data['detalle_gestion'],
            'fecha_respuesta'=>$data['fecha_respuesta'],
            'orden_id'=>$data['orden_id'],
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
        return $gestionAlternativa;
    }
    public function update($data, $gestionAlternativaId)
    {
        $gestionAlternativa = GestionAlternativa::findOrFail($gestionAlternativaId);
        $gestionAlternativa->update($data);
        return $gestionAlternativa;
    }
    public function destroy(GestionAlternativa $gestionAlternativa)
    {
        $gestionAlternativa->es_eliminado = 1;
        $gestionAlternativa->save();
        return $gestionAlternativa;
    }
    public function obtenerPorOrdenId($ordenId)
    {
        $gestionAlternativa = GestionAlternativa::where('orden_id', $ordenId)
                                  ->where('estado',Estado::ACTIVO)
                                  ->where('es_eliminado',0)
                                  ->get();
        return $gestionAlternativa;
    }

}
