<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posta extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'numero_posta',
        'plantilla_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Posta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function avancePlantilla()
    {
        return $this->belongsTo(AvancePlantilla::class, 'plantilla_id');
    }
}
