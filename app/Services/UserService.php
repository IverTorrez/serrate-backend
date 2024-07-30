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

}
