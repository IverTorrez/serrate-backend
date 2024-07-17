<?php

use App\Http\Controllers\AgendaApunteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvancePlantillaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CausaController;
use App\Http\Controllers\CausaPostaController;
use App\Http\Controllers\ClaseTribunalController;
use App\Http\Controllers\ConfirmacionController;
use App\Http\Controllers\CuerpoExpedienteController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\DevolucionSaldoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\InformePostaController;
use App\Http\Controllers\JuzgadoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MatrizCotizacionController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\PostaController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProcuraduriaDescargaController;
use App\Http\Controllers\TipoLegalController;
use App\Http\Controllers\TipoPostaController;
use App\Http\Controllers\TribunalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





Route::group(['prefix'=>'v1','namespace'=>'App\Http\Controllers'],function(){

    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);


    Route::middleware(['auth:sanctum'])->group(function (){
        Route::get('logout',[AuthController::class,'logout']);
        //Materia
        Route::get('materia',[MateriaController::class,'index']);
        Route::get('materia/{materia}',[MateriaController::class,'show']);
        Route::post('materia',[MateriaController::class,'store']);
        Route::patch('materia/{materia}',[MateriaController::class,'update']);
        Route::patch('materia/eliminar/{materia}',[MateriaController::class,'destroy']);
        //TipoLegal
        Route::get('tipo-legal',[TipoLegalController::class,'index']);
        Route::post('tipo-legal',[TipoLegalController::class,'store']);
        Route::get('tipo-legal/{tipoLegal}',[TipoLegalController::class,'show']);
        Route::patch('tipo-legal/{tipoLegal}',[TipoLegalController::class,'update']);
        Route::patch('tipo-legal/eliminar/{tipoLegal}',[TipoLegalController::class,'destroy']);
        //Categoria
        Route::get('categoria',[CategoriaController::class,'index']);
        Route::post('categoria',[CategoriaController::class,'store']);
        Route::get('categoria/{categoria}',[CategoriaController::class,'show']);
        Route::patch('categoria/{categoria}',[CategoriaController::class,'update']);
        Route::patch('categoria/eliminar/{categoria}',[CategoriaController::class,'destroy']);
        //Piso
        Route::get('piso',[PisoController::class,'index']);
        Route::post('piso',[PisoController::class,'store']);
        Route::get('piso/{piso}',[PisoController::class,'show']);
        Route::patch('piso/{piso}',[PisoController::class,'update']);
        Route::patch('piso/eliminar/{piso}',[PisoController::class,'destroy']);
        //Distrito
        Route::get('distrito',[DistritoController::class,'index']);
        Route::post('distrito',[DistritoController::class,'store']);
        Route::get('distrito/{distrito}',[DistritoController::class,'show']);
        Route::patch('distrito/{distrito}',[DistritoController::class,'update']);
        Route::patch('distrito/eliminar/{distrito}',[DistritoController::class,'destroy']);
        //Juzgado
        Route::get('juzgado',[JuzgadoController::class,'index']);
        Route::post('juzgado',[JuzgadoController::class,'store']);
        Route::get('juzgado/{juzgado}',[JuzgadoController::class,'show']);
        Route::patch('juzgado/{juzgado}',[JuzgadoController::class,'update']);
        Route::patch('juzgado/eliminar/{juzgado}',[JuzgadoController::class,'destroy']);
        //Clase Tribunal
        Route::get('clase-tribunal',[ClaseTribunalController::class,'index']);
        Route::post('clase-tribunal',[ClaseTribunalController::class,'store']);
        Route::get('clase-tribunal/{claseTribunal}',[ClaseTribunalController::class,'show']);
        Route::patch('clase-tribunal/{claseTribunal}',[ClaseTribunalController::class,'update']);
        Route::patch('clase-tribunal/eliminar/{claseTribunal}',[ClaseTribunalController::class,'destroy']);
        //Causa
        Route::get('causa',[CausaController::class,'index']);
        Route::post('causa',[CausaController::class,'store']);
        Route::get('causa/{causa}',[CausaController::class,'show']);
        Route::patch('causa/{causa}',[CausaController::class,'update']);
        Route::patch('causa/eliminar/{causa}',[CausaController::class,'destroy']);
        //Tribunal
        Route::get('tribunal',[TribunalController::class,'index']);
        Route::post('tribunal',[TribunalController::class,'store']);
        Route::get('tribunal/{tribunal}',[TribunalController::class,'show']);
        Route::patch('tribunal/{tribunal}',[TribunalController::class,'update']);
        Route::patch('tribunal/eliminar/{tribunal}',[TribunalController::class,'destroy']);
        //Cuerpo Expediente
        Route::get('cuerpo-expediente',[CuerpoExpedienteController::class,'index']);
        Route::post('cuerpo-expediente',[CuerpoExpedienteController::class,'store']);
        Route::get('cuerpo-expediente/{cuerpoExpediente}',[CuerpoExpedienteController::class,'show']);
        Route::patch('cuerpo-expediente/{cuerpoExpediente}',[CuerpoExpedienteController::class,'update']);
        Route::patch('cuerpo-expediente/eliminar/{cuerpoExpediente}',[CuerpoExpedienteController::class,'destroy']);
        //Participante
        Route::get('participante',[ParticipanteController::class,'index']);
        Route::post('participante',[ParticipanteController::class,'store']);
        Route::get('participante/{participante}',[ParticipanteController::class,'show']);
        Route::patch('participante/{participante}',[ParticipanteController::class,'update']);
        Route::patch('participante/eliminar/{participante}',[ParticipanteController::class,'destroy']);
        //Depositos
        Route::get('deposito',[DepositoController::class,'index']);
        Route::post('deposito',[DepositoController::class,'store']);
        Route::get('deposito/{deposito}',[DepositoController::class,'show']);
        Route::patch('deposito/{deposito}',[DepositoController::class,'update']);
        Route::patch('deposito/eliminar/{deposito}',[DepositoController::class,'destroy']);
        //Devolucion Saldo
        Route::get('devolucion-saldo',[DevolucionSaldoController::class,'index']);
        Route::post('devolucion-saldo',[DevolucionSaldoController::class,'store']);
        Route::get('devolucion-saldo/{devolucionSaldo}',[DevolucionSaldoController::class,'show']);
        Route::patch('devolucion-saldo/{devolucionSaldo}',[DevolucionSaldoController::class,'update']);
        Route::patch('devolucion-saldo/eliminar/{devolucionSaldo}',[DevolucionSaldoController::class,'destroy']);
        //Avance plantilla
        Route::get('avance-plantilla',[AvancePlantillaController::class,'index']);
        Route::post('avance-plantilla',[AvancePlantillaController::class,'store']);
        Route::get('avance-plantilla/{avancePlantilla}',[AvancePlantillaController::class,'show']);
        Route::patch('avance-plantilla/{avancePlantilla}',[AvancePlantillaController::class,'update']);
        Route::patch('avance-plantilla/eliminar/{avancePlantilla}',[AvancePlantillaController::class,'destroy']);
        //Posta
        Route::get('posta',[PostaController::class,'index']);
        Route::post('posta',[PostaController::class,'store']);
        Route::get('posta/{posta}',[PostaController::class,'show']);
        Route::patch('posta/{posta}',[PostaController::class,'update']);
        Route::patch('posta/eliminar/{posta}',[PostaController::class,'destroy']);
        //Agenda apunte
        Route::get('agenda-apunte',[AgendaApunteController::class,'index']);
        Route::post('agenda-apunte',[AgendaApunteController::class,'store']);
        Route::get('agenda-apunte/{agendaApunte}',[AgendaApunteController::class,'show']);
        Route::patch('agenda-apunte/{agendaApunte}',[AgendaApunteController::class,'update']);
        Route::patch('agenda-apunte/eliminar/{agendaApunte}',[AgendaApunteController::class,'destroy']);
        //Causa Posta
        Route::get('causa-posta',[CausaPostaController::class,'index']);
        Route::post('causa-posta',[CausaPostaController::class,'store']);
        Route::get('causa-posta/{causaPosta}',[CausaPostaController::class,'show']);
        Route::patch('causa-posta/{causaPosta}',[CausaPostaController::class,'update']);
        Route::patch('causa-posta/eliminar/{causaPosta}',[CausaPostaController::class,'destroy']);
        //Tipo posta
        Route::get('tipo-posta',[TipoPostaController::class,'index']);
        Route::post('tipo-posta',[TipoPostaController::class,'store']);
        Route::get('tipo-posta/{tipoPosta}',[TipoPostaController::class,'show']);
        Route::patch('tipo-posta/{tipoPosta}',[TipoPostaController::class,'update']);
        Route::patch('tipo-posta/eliminar/{tipoPosta}',[TipoPostaController::class,'destroy']);
        //Informe Posta
        Route::get('informe-posta',[InformePostaController::class,'index']);
        Route::post('informe-posta',[InformePostaController::class,'store']);
        Route::get('informe-posta/{informePosta}',[InformePostaController::class,'show']);
        Route::patch('informe-posta/{informePosta}',[InformePostaController::class,'update']);
        Route::patch('informe-posta/eliminar/{informePosta}',[InformePostaController::class,'destroy']);
        //Matriz cotizacion
        Route::get('matriz-cotizacion',[MatrizCotizacionController::class,'index']);
        Route::get('matriz-cotizacion/{matrizCotizacion}',[MatrizCotizacionController::class,'show']);
        Route::patch('matriz-cotizacion/{matrizCotizacion}',[MatrizCotizacionController::class,'update']);
        //Orden
        Route::get('orden',[OrdenController::class,'index']);
        Route::post('orden',[OrdenController::class,'store']);
        Route::get('orden/{orden}',[OrdenController::class,'show']);
        Route::patch('orden/{orden}',[OrdenController::class,'update']);
        Route::patch('orden/eliminar/{orden}',[OrdenController::class,'destroy']);
        Route::patch('orden/aceptar/{orden}',[OrdenController::class,'aceptarOrden']);
        //Cotizacion

       //Presupuesto
       Route::get('presupuesto',[PresupuestoController::class,'index']);
       Route::post('presupuesto',[PresupuestoController::class,'store']);
       Route::get('presupuesto/{presupuesto}',[PresupuestoController::class,'show']);
       Route::patch('presupuesto/{presupuesto}',[PresupuestoController::class,'update']);
       Route::patch('presupuesto/eliminar/{presupuesto}',[PresupuestoController::class,'destroy']);
       Route::patch('presupuesto/entregar/{presupuesto}',[PresupuestoController::class,'entregarPresupuesto']);
       //Procuraduria Descarga
       Route::get('descarga',[ProcuraduriaDescargaController::class,'index']);
       Route::post('descarga',[ProcuraduriaDescargaController::class,'store']);

       //Confirmacion
       Route::patch('confirmacion/pronuncio-abogado/{confirmacion}',[ConfirmacionController::class,'pronuncioAbogado']);
       Route::patch('confirmacion/pronuncio-contador/{confirmacion}',[ConfirmacionController::class,'pronuncioContador']);


    });

});


