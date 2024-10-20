<?php

namespace App\Models;

use App\Traits\CommonsScopesCausa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Causa extends Model
{
    use CommonsScopesCausa, HasFactory;
    protected $fillable = [
        'nombre',
        'observacion',
        'objetivos',
        'estrategia',
        'informacion',
        'apuntes_juridicos',
        'apuntes_honorarios',
        'tiene_billetera',
        'billetera',
        'saldo_devuelto',
        'color',
        'materia_id',
        'tipolegal_id',
        'categoria_id',
        'abogado_id',
        'procurador_id',
        'usuario_id',
        'plantilla_id',
        'estado',
        'es_eliminado'
    ];

    /**
     * Get the user that owns the Causa
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function tipoLegal()
    {
        return $this->belongsTo(TipoLegal::class, 'tipolegal_id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    public function abogado()
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }
    public function procurador()
    {
        return $this->belongsTo(User::class, 'procurador_id');
    }
    /**
     * Get all of the comments for the Causa
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tribunales()
    {
        return $this->hasMany(Tribunal::class, 'causa_id');
    }

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'causa_id');
    }
    public function depositos()
    {
        return $this->hasMany(Deposito::class, 'causa_id');
    }
    public function devolucionesSaldo()
    {
        return $this->hasMany(DevolucionSaldo::class, 'causa_id');
    }
    public function agendaApuntes()
    {
        return $this->hasMany(AgendaApunte::class, 'causa_id');
    }
    public function causaPostas()
    {
        return $this->hasMany(CausaPosta::class, 'causa_id');
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'causa_id');
    }
    public function paqueteCausas()
    {
        return $this->hasMany(PaqueteCausa::class, 'causa_id');
    }
}
