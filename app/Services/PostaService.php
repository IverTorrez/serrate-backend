<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Posta;
use Illuminate\Http\Request;

class PostaService
{
    public function update($data, $postaId)
    {
        $posta = Posta::findOrFail($postaId);
        $posta->update($data);
        return $posta;
    }
    public function listarPorAvancePlantillaId($avancePlantillaId)
    {
      $postas = Posta::where('estado', Estado::ACTIVO)
                     ->where('es_eliminado', 0)
                     ->where('plantilla_id', $avancePlantillaId)
                     ->orderBy('numero_posta', 'asc')
                     ->get();
      return $postas;
    }

}
