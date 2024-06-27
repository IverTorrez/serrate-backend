<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaseTribunal extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'estado',
        'es_eliminado'
    ];
}
