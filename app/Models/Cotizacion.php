<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;
    protected $fillable=[
        'compra',
        'venta',
        'penalizacion',
        'prioridad',
        'condicion',
        'orden_id',
        'estado',
        'es_eliminado'
    ];
}
