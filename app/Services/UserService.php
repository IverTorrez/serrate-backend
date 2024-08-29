<?php
namespace App\Services;

use App\Constants\Estado;
use App\Constants\TipoUsuario;
use App\Models\User;
use Illuminate\Http\Request;

class UserService
{
    public function update($data, $userId)
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user;
    }
    public function obtenerUnPMaestro(){
        $usuario = User::where('tipo', TipoUsuario::PROCURADOR_MAESTRO)
                       ->where('estado', Estado::ACTIVO)
                       ->where('es_eliminado', 0)
                       ->first();
        if ($usuario){
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

        if ($usuarios->isNotEmpty()){
            $usuarios->load('persona');//Carga el modelo de persona relacionada
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

        if ($usuarios->isNotEmpty()){
            $usuarios->load('persona');//Carga el modelo de persona relacionada
        return $usuarios;
        } else {
        return 'No se encontró ningún usuario Procurador.';
        }
    }

}
