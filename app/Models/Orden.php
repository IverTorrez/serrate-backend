<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    protected $fillable=[
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
        'es_eliminado'
    ];
}
