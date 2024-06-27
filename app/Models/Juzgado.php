<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Juzgado extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre_numerico',
        'jerarquia',
        'materia_juzgado',
        'coordenadas',
        'foto_url',
        'contacto1',
        'contacto2',
        'contacto3',
        'contacto4',
        'distrito_id',
        'piso_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Juzgado
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function piso()
    {
        return $this->belongsTo(Piso::class, 'piso_id');
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id');
    }
    /**
     * Get all of the comments for the Juzgado
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tribunales()
    {
        return $this->hasMany(Tribunal::class, 'juzgado_id');
    }
}
