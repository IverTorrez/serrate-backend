<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\CausaPosta;
use Illuminate\Http\Request;

class CausaPostaService
{
    public function store($data)
    {
        $causaPosta = CausaPosta::create([
            'nombre'=>$data['nombre'],
            'numero_posta'=>$data['numero_posta'],
            'copia_nombre_plantilla'=>$data['copia_nombre_plantilla'],
            'tiene_informe'=>$data['tiene_informe'],
            'causa_id'=>$data['causa_id'],
            'estado'=>Estado::ACTIVO,
            'es_eliminado'=>0
         ]);
        return $causaPosta;
    }
    public function update($data, $causaPostaId)
    {
        $causaPosta = CausaPosta::findOrFail($causaPostaId);
        $causaPosta->update($data);
        return $causaPosta;
    }
    public function eliminarPorCausaId($causaId)
    {
        CausaPosta::where('causa_id', $causaId)
                  ->update([
                            'estado' => Estado::INACTIVO,
                            'es_eliminado' => 1
                           ]);
    }

}
