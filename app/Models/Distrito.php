<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'abreviatura',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get all of the comments for the Distrito
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function juzgados()
    {
        return $this->hasMany(Juzgado::class, 'distrito_id');
    }
}
