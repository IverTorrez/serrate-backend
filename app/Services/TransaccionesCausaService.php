<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\TransaccionesCausa;
use App\Models\Video;
use Illuminate\Http\Request;

class TransaccionesCausaService
{
    public function store($data)
    {
        $transaccionesCausa = TransaccionesCausa::create([
            'monto' => $data['monto'],
            'fecha_transaccion' => $data['fecha_transaccion'],
            'tipo' => $data['tipo'],
            'transaccion' => $data['transaccion'],
            'glosa' => $data['glosa'],
            'causa_id' => $data['causa_id'],
            'causa_origen_destino' => $data['causa_origen_destino'],
            'usuario_id' => $data['usuario_id'],
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,

        ]);
        return $transaccionesCausa;
    }
    public function update($data, $transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        $transaccionesCausa->update($data);
        return $transaccionesCausa;
    }
    public function listarActivos()
    {
        $transaccionesCausa = TransaccionesCausa::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $transaccionesCausa;
    }
    public function destroy($transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        $transaccionesCausa->es_eliminado = 1;
        $transaccionesCausa->save();
        return $transaccionesCausa;
    }
    public function obtenerUno($transaccionesCausaId)
    {
        $transaccionesCausa = TransaccionesCausa::findOrFail($transaccionesCausaId);
        return $transaccionesCausa;
    }
    public function obtenerTransaccionesDeCausa(Request $request, $causaId)
    {
        try {
            $query = TransaccionesCausa::select([
                'id',
                'monto',
                'fecha_transaccion',
                'tipo',
                'transaccion',
                'glosa',
                'causa_id',
                'causa_origen_destino',
                'estado'
            ])->active()
            ->where('estado', Estado::ACTIVO)
                ->where('es_eliminado', 0);
            $query->where('causa_id', $causaId);

            if ($request->has('search')) {
                $search = json_decode($request->input('search'), true);
                $query->search($search);
            }

            if ($request->has('sort')) {
                $sort = json_decode($request->input('sort'), true);
                $query->sort($sort);
            }

            $perPage = $request->input('perPage', 10);
            return $query->paginate($perPage);

            //$result = $query->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener las transacciones de causa.'], 500);
        }
    }
}
