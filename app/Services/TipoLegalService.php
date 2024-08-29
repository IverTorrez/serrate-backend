<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\TipoLegal;
use Illuminate\Http\Request;

class TipoLegalService
{
    public function update($data, $tipoLegalId)
    {
        $tipoLegal = TipoLegal::findOrFail($tipoLegalId);
        $tipoLegal->update($data);
        return $tipoLegal;
    }
    public function listarPorMateriaId($materiaId)
    {
      $tipoLegal = TipoLegal::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->where('materia_id', $materiaId)
                     ->orderBy('nombre', 'asc')
                     ->get();
      return $tipoLegal;
    }
    public function listarActivos()
    {
        $tipoLegal = TipoLegal::where('estado', Estado::ACTIVO)
                              ->where('es_eliminado', 0)
                              ->get();
        return $tipoLegal;
    }

}
