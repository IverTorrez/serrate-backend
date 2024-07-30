<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'precio',
        'cantidad_mes',
        'cantidad_causas',
        'descripcion',
        'fecha_creacion',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
}
