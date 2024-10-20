<?php

namespace App\Models;

use App\Traits\CommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billetera extends Model
{
    use CommonScopes, HasFactory;
    protected $fillable=[
        'monto',
        'abogado_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Billetera
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function abogado()
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }
}
