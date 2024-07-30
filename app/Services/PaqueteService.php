<?php
namespace App\Services;

use App\Constants\Estado;
use Illuminate\Http\Request;
use App\Models\Paquete;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaqueteService
{
    public function index()
    {
        $paquete = Paquete::where('es_eliminado', 0)
                          ->where('estado', Estado::ACTIVO)
                          ->paginate();
        return $paquete;
    }

    public function store($data)
    {
        $paquete = Paquete::create([
            'nombre' => $data['nombre'],
            'precio' => $data['precio'],
            'cantidad_mes' => $data['cantidad_mes'],
            'cantidad_causas' => $data['cantidad_causas'],
            'descripcion' => $data['descripcion'],
            'fecha_creacion' => $data['fecha_creacion'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0
        ]);
        return $paquete;
    }
    public function update($data, $paqueteId)
    {
        $paquete = Paquete::findOrFail($paqueteId);
        $paquete->update($data);
        return $paquete;
    }
    public function destroy(Paquete $paquete)
    {
        $paquete->es_eliminado = 1;
        $paquete->save();
        return $paquete;
    }
    public function obtenerUno($paqueteId)
    {
        $paquete = Paquete::find($paqueteId);
        if (!$paquete) {
            throw new ModelNotFoundException('El paquete con ID ' . $paqueteId . ' no existe.');
        }
        return $paquete;
    }


}
