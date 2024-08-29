<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoService
{
    public function store($data)
    {
        $documento = Documento::create([
            'nombre' => $data['nombre'],
            'archivo_url' => $data['archivo_url'],
            'tipo' => $data['tipo'],
            'categoria_id' => $data['categoria_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $documento;
    }
    public function update($data, $documentoId)
    {
        $documento = Documento::findOrFail($documentoId);
        $documento->update($data);
        return $documento;
    }
    public function destroy(Documento $documento)
    {
        $documento->es_eliminado = 1;
        $documento->save();
        return $documento;
    }
}
