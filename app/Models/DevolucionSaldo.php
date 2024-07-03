<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionSaldo extends Model
{
    use HasFactory;
    protected $fillable=[
        'fecha_devolucion',
        'detalle_devolucion',
        'monto',
        'causa_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the causa that owns the DevolucionSaldo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function causa()
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }
}
