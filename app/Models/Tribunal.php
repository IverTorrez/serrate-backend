<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tribunal extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable = [
        'expediente',
        'codnurejianuj',
        'link_carpeta',
        'clasetribunal_id',
        'causa_id',
        'juzgado_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Tribunal
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
    public function claseTribunal()
    {
        return $this->belongsTo(ClaseTribunal::class, 'clasetribunal_id');
    }
    public function juzgado()
    {
        return $this->belongsTo(Juzgado::class, 'juzgado_id');
    }
    /**
     * Get all of the cuerpoExpedientes for the Tribunal
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cuerpoExpedientes()
    {
        return $this->hasMany(CuerpoExpediente::class, 'tribunal_id');
    }
}
