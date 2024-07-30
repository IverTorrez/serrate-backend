<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraPaquete extends Model
{
    use HasFactory;
    protected $fillable=[
        'monto',
        'fecha_ini_vigencia',
        'fecha_fin_vigencia',
        'fecha_compra',
        'cantidad_causas',
        'dias_vigente',
        'paquete_id',
        'usuario_id',
        'estado',
        'es_eliminado'
    ];
}
