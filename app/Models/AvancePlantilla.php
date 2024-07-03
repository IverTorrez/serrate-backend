<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvancePlantilla extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the postas for the AvancePlantilla
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function postas()
    {
        return $this->hasMany(Posta::class, 'plantilla_id');
    }
}
