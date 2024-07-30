<?php
namespace App\Services;

use App\Constants\Estado;
use App\Constants\EstadoCausa;
use App\Models\Causa;
use Illuminate\Http\Request;

class CausaService
{
    public function store($data)
    {
        $causa=Causa::create([
            'nombre'=>$data['nombre'],
            'observacion'=>$data['observacion'],
            'objetivos'=>$data['objetivos'],
            'estrategia'=>$data['estrategia'],
            'informacion'=>$data['informacion'],
            'apuntes_juridicos'=>$data['apuntes_juridicos'],
            'apuntes_honorarios'=>$data['apuntes_honorarios'],
            'tiene_billetera'=>$data['tiene_billetera'],
            'billetera'=>$data['billetera'],
            'saldo_devuelto'=>$data['saldo_devuelto'],
            'color'=>$data['color'],
            'materia_id'=>$data['materia_id'],
            'tipolegal_id'=>$data['tipolegal_id'],
            'categoria_id'=>$data['categoria_id'],
            'abogado_id'=>$data['abogado_id'],
            'procurador_id'=>$data['procurador_id'],
            'usuario_id'=>$data['usuario_id'],
            'plantilla_id' => $data['plantilla_id'],

            'estado'=>EstadoCausa::ACTIVA,
            'es_eliminado'=>0
         ]);
        return $causa;
    }
    public function update($data, $causaId)
    {
        $causa = Causa::findOrFail($causaId);
        $causa->update($data);
        return $causa;
    }

}
