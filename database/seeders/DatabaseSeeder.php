<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
           MateriaSeeder::class,
           TipoLegalSeeder::class,
           UsuarioSeeder::class,//Siempre se carga
           CategoriaSeeder::class,
           PisoSeeder::class,
           DistritoSeeder::class,
           JuzgadoSeeder::class,
           ClaseTribunalSeeder::class,
           CausaSeeder::class,
           TribunalSeeder::class,
           CuerpoExpedienteSeeder::class,
           ParticipanteSeeder::class,
           DepositoSeeder::class,
           DevolucionSaldoSeeder::class,
           TablaConfigSeeder::class, //Siempre se carga
           AvancePlantillaSeeder::class,
           PostaSeeder::class,
           AgendaApunteSeeder::class,
           CausaPostaSeeder::class,
           TipoPostaSeeder::class,
           InformePostaSeeder::class,
           MatrizCotizacionSeeder::class, //Siempre se carga
           OrdenSeeder::class,
           CotizacionSeeder::class,
           PresupuestoSeeder::class,
           ProcuraduriaDesacargaSeeder::class,
           PaqueteSeeder::class,
           DocumentosCategoriaSeeder::class,
        ]);
    }
}
