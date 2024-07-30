<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TablaConfig extends Model
{
    use HasFactory;
    protected $fillable=[
        'caja_contador',
        'deuda_extarna',
        'ganancia_procesal_procuraduria',
        'imagen_index',
        'doc_aranceles',
        'doc_normas',
        'estado',
        'es_eliminado'
    ];
}
