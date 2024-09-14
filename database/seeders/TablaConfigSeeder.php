<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Estado;
use App\Models\TablaConfig;

class TablaConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estado = Estado::ACTIVO;
        TablaConfig::create([
            'caja_contador' => 0,
            'deuda_extarna' => 0,
            'ganancia_procesal_procuraduria' => 0,
            'titulo_index' => null,
            'texto_index' => null,
            'imagen_index' => null,
            'imagen_logo' => null,
            'estado' => $estado,
            'es_eliminado' => 0,
        ]);
    }
}
