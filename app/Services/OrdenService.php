<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Orden;

class OrdenService
{
    public function index($request)
    {
        return $this->getOrdenes($request);
    }

    public function listarPorCausa($request, $idCausa)
    {
        return $this->getOrdenes($request, $idCausa);
    }

    public function getOrdenes($request, $idCausa = null)
    {
        try {
            $query = Orden::select([
                'id',
                'entrega_informacion',
                'entrega_documentacion',
                'fecha_inicio',
                'fecha_fin',
                'fecha_giro',
                'plazo_hora',
                'fecha_recepcion',
                'etapa_orden',
                'calificacion',
                'prioridad',
                'fecha_cierre',
                'girada_por',
                'fecha_ini_bandera',
                'notificado',
                'lugar_ejecucion',
                'sugerencia_presupuesto',
                'tiene_propina',
                'propina',
                'causa_id',
                'procurador_id',
                'matriz_id',
                'estado',
            ])
                ->with([
                    'causa:id,nombre',
                    'procurador:id,name,email,tipo,estado',
                    'procurador.persona:usuario_id,nombre,apellido,telefono,direccion',
                    'matriz:id,numero_prioridad,precio_compra,penalizacion'

                ])
                ->active();

            if ($idCausa) {
                $query->where('causa_id', $idCausa);
            }

            if ($request->has('search')) {
                $search = json_decode($request->input('search'), true);
                $query->search($search);
            }

            if ($request->has('sort')) {
                $sort = json_decode($request->input('sort'), true);
                $query->sort($sort);
            }

            $perPage = $request->input('perPage', 10);
            return $query->paginate($perPage);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }


    public function listarOrden(Orden $orden = null)
    {
        try {
            $query = Orden::select([
                'id',
                'entrega_informacion',
                'entrega_documentacion',
                'fecha_inicio',
                'fecha_fin',
                'fecha_giro',
                'plazo_hora',
                'fecha_recepcion',
                'etapa_orden',
                'calificacion',
                'prioridad',
                'fecha_cierre',
                'girada_por',
                'fecha_ini_bandera',
                'notificado',
                'lugar_ejecucion',
                'sugerencia_presupuesto',
                'tiene_propina',
                'propina',
                'causa_id',
                'procurador_id',
                'matriz_id',
                'estado',
            ])
                ->with([
                    'causa:id,nombre,materia_id,tipolegal_id',
                    'causa.materia:id,abreviatura',
                    'causa.tipoLegal:id,abreviatura',
                    'procurador:id,name,email,tipo,estado',
                    'procurador.persona:usuario_id,nombre,apellido,telefono,direccion',
                    'matriz:id,numero_prioridad,precio_compra,penalizacion'
                ])
                ->active();

            if ($orden) {
                $query->where('id', $orden->id);
                $result = $query->first();

                if (!$result) {
                    return response()->json(['message' => 'Orden no encontrada.'], 404);
                }

                return [
                    'message' => 'Orden obtenida correctamente',
                    'data' => $result
                ];
            }

            $result = $query->get();

            return [
                'message' => 'Órdenes obtenidas correctamente',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las órdenes.'], 500);
        }
    }

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
            'notificado' => $data['notificado'],


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
