<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Orden;
use Illuminate\Http\Request;
use App\Constants\EtapaOrden;

class OrdenService
{
    public function store($data)
    {
        $orden = Orden::create([
            'entrega_informacion' => $data['entrega_informacion'],
            'entrega_documentacion' => $data['entrega_documentacion'],
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'fecha_giro' => $data['fecha_giro'],
            'plazo_hora' => $data['plazo_hora'],
            'fecha_recepcion' => $data['fecha_recepcion'],
            'etapa_orden' => $data['etapa_orden'],
            'calificacion' => '',
            'prioridad' => $data['prioridad'],
            'fecha_cierre' => null,
            'girada_por' => $data['girada_por'],
            'fecha_ini_bandera' => $data['fecha_ini_bandera'],
            'notificado'=>$data['notificado'],


            'lugar_ejecucion' => $data['lugar_ejecucion'],
            'sugerencia_presupuesto' => $data['sugerencia_presupuesto'],
            'tiene_propina' => $data['tiene_propina'],
            'propina' => $data['propina'],
            'causa_id' => $data['causa_id'],
            'procurador_id' => $data['procurador_id'],
            'matriz_id' => $data['matriz_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $orden;
    }
    public function update($data, $ordenId)
    {
        $orden = Orden::findOrFail($ordenId);
        $orden->update($data);
        return $orden;
    }

}
