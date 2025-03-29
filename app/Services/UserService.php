<?php

namespace App\Services;

use App\Constants\ErrorMessages;
use App\Constants\Estado;
use App\Constants\SuccessMessages;
use App\Constants\TipoUsuario;
use App\Models\Billetera;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function obtenerUsuariosDependientes($request, $abogadoId)
    {
        $query = User::where('abogado_id', $abogadoId)
            ->active()
            ->with('persona')
            ->select('users.*');

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
    }

    public function crearUsuario(array $data): JsonResponse
    {
        try {
            DB::transaction(fn() => $this->createUserWithRelations($data));
            return ResponseService::success(message: SuccessMessages::CREADO_CORRECTAMENTE);
        } catch (\Exception $e) {
            Log::error('Error al crear registro: ' . $e->getMessage());
            return ResponseService::error(ErrorMessages::ERROR_CREAR, 500);
        }
    }
    private function createUserWithRelations(array $data): void
    {
        $abogado_id = (auth()->check() && $data['tipo'] === TipoUsuario::ABOGADO_DEPENDIENTE) ? auth()->id() : 0;

        $user = User::create([
            'name' => $data['persona']['nombre'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipo' => $data['tipo'],
            'abogado_id' => $abogado_id,
            'opciones_moto' => isset($data['opciones_moto']) ? json_encode($data['opciones_moto']) : null,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => false,
        ]);

        $this->createPersona($data, $user);
        $this->createBilletera($data, $user);
    }

    private function createPersona(array $data, User $user): void
    {
        Persona::create([
            'nombre' => $data['persona']['nombre'],
            'apellido' => $data['persona']['apellido'],
            'telefono' => $data['persona']['telefono'],
            'direccion' => $data['persona']['direccion'] ?? null,
            'coordenadas' => $data['coordenadas'] ?? null,
            'observacion' => $data['observacion'] ?? null,
            'foto_url' => $data['foto_url'] ?? null,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => false,
            'usuario_id' => $user->id,
        ]);
    }

    private function createBilletera(array $data, User $user): void
    {
        try {
            if (!in_array($data['tipo'], [TipoUsuario::ABOGADO_INDEPENDIENTE, TipoUsuario::ABOGADO_LIDER])) {
                return;
            }

            Billetera::create([
                'monto'        => 0,
                'abogado_id'   => $user->id,
                'estado'       => Estado::ACTIVO,
                'es_eliminado' => false,
            ]);

            Log::info('Billetera created for user: ' . $user->id);
        } catch (\Exception $e) {
            Log::error('Error in createBilletera: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($data, $userId)
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user;
    }
    public function obtenerUnPMaestro()
    {
        $usuario = User::where('tipo', TipoUsuario::PROCURADOR_MAESTRO)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->first();
        if ($usuario) {
            return $usuario;
        } else {
            return 'No se encontró ningún usuario PROCURADOR_MAESTRO.';
        }
    }
    public function listarAbogados()
    {
        $usuarios =  User::whereIn('tipo', [
            TipoUsuario::ABOGADO_DEPENDIENTE,
            TipoUsuario::ABOGADO_INDEPENDIENTE,
            TipoUsuario::ABOGADO_LIDER
        ])
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();

        if ($usuarios->isNotEmpty()) {
            $usuarios->load('persona'); //Carga el modelo de persona relacionada
            return $usuarios;
        } else {
            return 'No se encontró ningún usuario ABOGADO.';
        }
    }
    public function listarAbogadosDependientes($abogadoId)
    {
        $usuarios =  User::whereIn('tipo', [
            TipoUsuario::ABOGADO_DEPENDIENTE
        ])
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('abogado_id', $abogadoId)
            ->get();

        if ($usuarios->isNotEmpty()) {
            $usuarios->load('persona'); //Carga el modelo de persona relacionada
            return $usuarios;
        } else {
            return 'No se encontró ningún usuario ABOGADO.';
        }
    }
    public function abogadosDependientes()
    {
        $usuarios =  User::where('tipo', TipoUsuario::ABOGADO_DEPENDIENTE)
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->where('abogado_id', Auth::user()->id)
            ->get();

        if ($usuarios->isNotEmpty()) {
            $usuarios->load('persona'); //Carga el modelo de persona relacionada
            return $usuarios;
        } else {
            return 'No se encontró ningún usuario ABOGADO.';
        }
    }

    public function listarActivos()
    {
        $usuarios = User::where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();
        return $usuarios;
    }
    public function listarProcuradores()
    {
        $usuarios =  User::whereIn('tipo', [
            TipoUsuario::PROCURADOR,
            TipoUsuario::PROCURADOR_MAESTRO
        ])
            ->where('estado', Estado::ACTIVO)
            ->where('es_eliminado', 0)
            ->get();

        if ($usuarios->isNotEmpty()) {
            $usuarios->load('persona'); //Carga el modelo de persona relacionada
            return $usuarios;
        } else {
            return 'No se encontró ningún usuario Procurador.';
        }
    }
}
