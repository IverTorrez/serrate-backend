<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcuraduriaDescarga extends Model
{
    use HasFactory;
    protected $fillable=[
        'detalle_informacion',
        'detalle_documentacion',
        'ultima_foja',
        'gastos',
        'saldo',
        'detalle_gasto',
        'fecha_descarga',
        'compra_judicial',
        'es_validado',
        'orden_id',
        'estado',
        'es_eliminado'
    ];


}
