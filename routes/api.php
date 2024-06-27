<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CausaController;
use App\Http\Controllers\ClaseTribunalController;
use App\Http\Controllers\CuerpoExpedienteController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\JuzgadoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\TipoLegalController;
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
        Route::get('tipolegal',[TipoLegalController::class,'index']);
        Route::post('tipolegal',[TipoLegalController::class,'store']);
        Route::get('tipolegal/{tipoLegal}',[TipoLegalController::class,'show']);
        Route::patch('tipolegal/{tipoLegal}',[TipoLegalController::class,'update']);
        Route::patch('tipolegal/eliminar/{tipoLegal}',[TipoLegalController::class,'destroy']);
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
        Route::get('clasetribunal',[ClaseTribunalController::class,'index']);
        Route::post('clasetribunal',[ClaseTribunalController::class,'store']);
        Route::get('clasetribunal/{claseTribunal}',[ClaseTribunalController::class,'show']);
        Route::patch('clasetribunal/{claseTribunal}',[ClaseTribunalController::class,'update']);
        Route::patch('clasetribunal/eliminar/{claseTribunal}',[ClaseTribunalController::class,'destroy']);
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
        Route::get('cuerpoexpediente',[CuerpoExpedienteController::class,'index']);
        Route::post('cuerpoexpediente',[CuerpoExpedienteController::class,'store']);
        Route::get('cuerpoexpediente/{cuerpoExpediente}',[CuerpoExpedienteController::class,'show']);
        Route::patch('cuerpoexpediente/{cuerpoExpediente}',[CuerpoExpedienteController::class,'update']);
        Route::patch('cuerpoexpediente/eliminar/{cuerpoExpediente}',[CuerpoExpedienteController::class,'destroy']);
        //Participante
        Route::get('participante',[ParticipanteController::class,'index']);
        Route::post('participante',[ParticipanteController::class,'store']);
        Route::get('participante/{participante}',[ParticipanteController::class,'show']);
        Route::patch('participante/{participante}',[ParticipanteController::class,'update']);
        Route::patch('participante/eliminar/{participante}',[ParticipanteController::class,'destroy']);

    });

});


