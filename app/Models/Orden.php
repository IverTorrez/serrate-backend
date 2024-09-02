<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
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

    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
    public function procurador()
    {
        return $this->belongsTo(User::class, 'procurador_id');
    }
    public function matriz()
    {
        return $this->belongsTo(MatrizCotizacion::class, 'matriz_id');
    }
}
