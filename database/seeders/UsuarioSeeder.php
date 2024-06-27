<?php

namespace Database\Seeders;

use App\Constants\TipoUsuario;
use App\Constants\Estado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Support\Facades\Hash;


class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datosJson=[
            // 'NO_MANEJA' => true,
            // 'SI_MANEJA_NO_TIENE' => false,
            // 'SI_MANEJA_SI_TIENE' => false,
        ];
        $tipoUsuario=TipoUsuario::ADMINISTRADOR;
        $estado=Estado::ACTIVO;
        $user = User::create([
            'name'=>'Admin',
            'email'=>'admin@example.com',
            'password'=>Hash::make('admin123'),
            'tipo'=>$tipoUsuario,
            'abogado_id'=>0,
            'opciones_moto'=>json_encode($datosJson),
            'estado'=>$estado,
            'es_eliminado'=>0
        ]);

        Persona::create([
          'nombre'=>$user->name,
          'apellido'=>'nuevo',
          'telefono'=>'76765443',
          'direccion'=>'Calle sur',
          'coordenadas'=>'',
          'observacion'=>'',
          'foto_url'=>'',
          'estado'=>$estado,
          'es_eliminado'=>0,
          'usuario_id'=>$user->id,

        ]);
       //ABOGADO INDEPENDIENTE
        $user = User::create([
            'name'=>'Leonel',
            'email'=>'abogado@example.com',
            'password'=>Hash::make('abogado123'),
            'tipo'=>TipoUsuario::ABOGADO_INDEPENDIENTE,
            'abogado_id'=>0,
            'opciones_moto'=>json_encode($datosJson),
            'estado'=>$estado,
            'es_eliminado'=>0
        ]);
        Persona::create([
            'nombre'=>$user->name,
            'apellido'=>'Hots',
            'telefono'=>'66565656',
            'direccion'=>'Springfield',
            'coordenadas'=>'',
            'observacion'=>'',
            'foto_url'=>'',
            'estado'=>$estado,
            'es_eliminado'=>0,
            'usuario_id'=>$user->id,

          ]);
         //PROCURADOR MAESTRO
          $user = User::create([
            'name'=>'Lalo',
            'email'=>'pmaestro@example.com',
            'password'=>Hash::make('pmaestro123'),
            'tipo'=>TipoUsuario::PROCURADOR_MAESTRO,
            'abogado_id'=>0,
            'opciones_moto'=>json_encode($datosJson),
            'estado'=>$estado,
            'es_eliminado'=>0
        ]);
        Persona::create([
            'nombre'=>$user->name,
            'apellido'=>'Landa',
            'telefono'=>'77764654',
            'direccion'=>'Springfield',
            'coordenadas'=>'',
            'observacion'=>'',
            'foto_url'=>'',
            'estado'=>$estado,
            'es_eliminado'=>0,
            'usuario_id'=>$user->id,
          ]);
          //PROCURADOR
          $user = User::create([
            'name'=>'Bart',
            'email'=>'procurador@example.com',
            'password'=>Hash::make('procurador123'),
            'tipo'=>TipoUsuario::PROCURADOR,
            'abogado_id'=>0,
            'opciones_moto'=>json_encode($datosJson),
            'estado'=>$estado,
            'es_eliminado'=>0
        ]);
        Persona::create([
            'nombre'=>$user->name,
            'apellido'=>'Simpson',
            'telefono'=>'60999898',
            'direccion'=>'Springfield',
            'coordenadas'=>'',
            'observacion'=>'',
            'foto_url'=>'',
            'estado'=>$estado,
            'es_eliminado'=>0,
            'usuario_id'=>$user->id,
          ]);
        //CONTADOR
        $user = User::create([
            'name'=>'Lisa',
            'email'=>'contador@example.com',
            'password'=>Hash::make('contador123'),
            'tipo'=>TipoUsuario::CONTADOR,
            'abogado_id'=>0,
            'opciones_moto'=>json_encode($datosJson),
            'estado'=>$estado,
            'es_eliminado'=>0
        ]);
        Persona::create([
            'nombre'=>$user->name,
            'apellido'=>'Simpson',
            'telefono'=>'77777676',
            'direccion'=>'Springfield',
            'coordenadas'=>'',
            'observacion'=>'',
            'foto_url'=>'',
            'estado'=>$estado,
            'es_eliminado'=>0,
            'usuario_id'=>$user->id,
          ]);
    }
}
