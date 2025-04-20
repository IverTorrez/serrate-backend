<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\ParametroVigencia;
use Illuminate\Http\Request;

class ParametroVigenciaService
{
    public function store($data)
    {
        $parametroVigencia = ParametroVigencia::create([
            'fecha_ultima_vigencia' => $data['fecha_ultima_vigencia'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $parametroVigencia;
    }

    public function update($data, $parametroVigenciaId)
    {
        $parametroVigencia = ParametroVigencia::findOrFail($parametroVigenciaId);
        $parametroVigencia->update($data);
        return $parametroVigencia;
    }
    public function obtenerUno($parametroVigenciaId)
    {
        $parametroVigencia = ParametroVigencia::findOrFail($parametroVigenciaId);
        return $parametroVigencia;
    }
    public function listarActivos()
    {
        $parametroVigencia = ParametroVigencia::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->get();
      return $parametroVigencia;
    }
    public function obtenerUnoPorUsuario($usuarioId)
    {
        $parametroVigencia = ParametroVigencia::where('usuario_id', $usuarioId)->first();
        return $parametroVigencia;
    }

}
