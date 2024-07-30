<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\Paquete;
use Carbon\Carbon;

class PaqueteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now=Carbon::now('America/La_Paz');
        $fechaHora=$now->toDateTimeString();
        Paquete::create([
            'nombre' => 'Paquete Principiante',
            'precio' => 200,
            'cantidad_mes' => 2,
            'cantidad_causas'=> 6,
            'descripcion' => 'Paquete unipersonal para 6 causas por dos meses',
            'fecha_creacion'=> $fechaHora,
            'usuario_id'=> 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Paquete::create([
            'nombre' => 'Paquete platino',
            'precio' => 350,
            'cantidad_mes' => 3,
            'cantidad_causas'=> 9,
            'descripcion' => 'Paquete unipersonal para 9 causas por tres meses',
            'fecha_creacion'=> $fechaHora,
            'usuario_id'=> 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
        Paquete::create([
            'nombre' => 'Paquete Oro',
            'precio' => 500,
            'cantidad_mes' => 5,
            'cantidad_causas'=> 15,
            'descripcion' => 'Paquete unipersonal para 15 causas por 5 meses',
            'fecha_creacion'=> $fechaHora,
            'usuario_id'=> 1,
            'estado' => Estado::ACTIVO,
            'es_eliminado' => 0,
        ]);
    }
}
