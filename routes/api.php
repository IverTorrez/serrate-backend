<?php

use App\Http\Controllers\AgendaApunteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvancePlantillaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CausaController;
use App\Http\Controllers\CausaPostaController;
use App\Http\Controllers\ClaseTribunalController;
use App\Http\Controllers\CompraPaqueteController;
use App\Http\Controllers\ConfirmacionController;
use App\Http\Controllers\CuerpoExpedienteController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\DevolucionSaldoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentosCategoriaController;
use App\Http\Controllers\GestionAlternativaController;
use App\Http\Controllers\InformePostaController;
use App\Http\Controllers\JuzgadoController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MatrizCotizacionController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PisoController;
use App\Http\Controllers\PostaController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProcuraduriaDescargaController;
use App\Http\Controllers\TipoLegalController;
use App\Http\Controllers\TipoPostaController;
use App\Http\Controllers\TribunalController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function () {

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);


    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);
        //Materia
        Route::get('materias', [MateriaController::class, 'index']);
        Route::get('materias/listar/{materia?}', [MateriaController::class, 'show']);
        Route::post('materias', [MateriaController::class, 'store']);
        Route::patch('materias/{materia}', [MateriaController::class, 'update']);
        Route::patch('materias/eliminar/{materia}', [MateriaController::class, 'destroy']);
        //TipoLegal
        Route::get('tipo-legal', [TipoLegalController::class, 'index']);
        Route::post('tipo-legal', [TipoLegalController::class, 'store']);
        Route::get('tipo-legal/listar/{tipoLegal?}', [TipoLegalController::class, 'show']);
        Route::patch('tipo-legal/{tipoLegal}', [TipoLegalController::class, 'update']);
        Route::patch('tipo-legal/eliminar/{tipoLegal}', [TipoLegalController::class, 'destroy']);
        Route::get('tipo-legal/materia/{materiaId}', [TipoLegalController::class, 'listarPorMateriaId']);
        //Categoria
        Route::get('categorias', [CategoriaController::class, 'index']);
        Route::post('categorias', [CategoriaController::class, 'store']);
        Route::get('categorias/listar/{categoria?}', [CategoriaController::class, 'show']);
        Route::patch('categorias/{categoria}', [CategoriaController::class, 'update']);
        Route::patch('categorias/eliminar/{categoria}', [CategoriaController::class, 'destroy']);
        //Piso
        Route::get('pisos', [PisoController::class, 'index']);
        Route::post('pisos', [PisoController::class, 'store']);
        Route::get('pisos/listar/{piso?}', [PisoController::class, 'show']);
        Route::patch('pisos/{piso}', [PisoController::class, 'update']);
        Route::patch('pisos/eliminar/{piso}', [PisoController::class, 'destroy']);
        //Distrito
        Route::get('distritos', [DistritoController::class, 'index']);
        Route::post('distritos', [DistritoController::class, 'store']);
        Route::get('distritos/listar/{distrito?}', [DistritoController::class, 'show']);
        Route::patch('distritos/{distrito}', [DistritoController::class, 'update']);
        Route::patch('distritos/eliminar/{distrito}', [DistritoController::class, 'destroy']);
        //Juzgado
        Route::get('juzgados', [JuzgadoController::class, 'index']);
        Route::post('juzgados', [JuzgadoController::class, 'store']);
        Route::get('juzgados/listar/{juzgado?}', [JuzgadoController::class, 'show']);
        Route::post('juzgados/{juzgado}', [JuzgadoController::class, 'update']); //Actualiza
        Route::patch('juzgados/eliminar/{juzgado}', [JuzgadoController::class, 'destroy']);
        //Clase Tribunal
        Route::get('clase-tribunal', [ClaseTribunalController::class, 'index']);
        Route::post('clase-tribunal', [ClaseTribunalController::class, 'store']);
        Route::get('clase-tribunal/{claseTribunal}', [ClaseTribunalController::class, 'show']);
        Route::get('clase-tribunal/activos/listar', [ClaseTribunalController::class, 'listarActivos']);
        Route::patch('clase-tribunal/{claseTribunal}', [ClaseTribunalController::class, 'update']);
        Route::patch('clase-tribunal/eliminar/{claseTribunal}', [ClaseTribunalController::class, 'destroy']);
        //Causa
        Route::get('causas', [CausaController::class, 'index']);
        Route::post('causas', [CausaController::class, 'store']);
        Route::get('causas/{causa}', [CausaController::class, 'show']);
        Route::patch('causas/{causa}', [CausaController::class, 'update']);
        Route::patch('causas/eliminar/{causa}', [CausaController::class, 'destroy']);
        //Tribunal
        Route::get('tribunal', [TribunalController::class, 'index']);
        Route::post('tribunal', [TribunalController::class, 'store']);
        Route::get('tribunal/{tribunal}', [TribunalController::class, 'show']);
        Route::get('tribunal/causa/listar/{causaId}', [TribunalController::class, 'listarActivosPorCausa']);
        Route::patch('tribunal/{tribunal}', [TribunalController::class, 'update']);
        Route::patch('tribunal/eliminar/{tribunal}', [TribunalController::class, 'destroy']);
        //Cuerpo Expediente
        Route::get('cuerpo-expedientes', [CuerpoExpedienteController::class, 'index']);
        Route::get('cuerpo-expedientes/tribunal/listar/{tribunalId}', [CuerpoExpedienteController::class, 'listadoPorTribunal']);
        Route::post('cuerpo-expedientes', [CuerpoExpedienteController::class, 'store']);
        Route::get('cuerpo-expedientes/{cuerpoExpediente}', [CuerpoExpedienteController::class, 'show']);
        Route::post('cuerpo-expedientes/{cuerpoExpediente}', [CuerpoExpedienteController::class, 'update']);
        Route::patch('cuerpo-expedientes/eliminar/{cuerpoExpediente}', [CuerpoExpedienteController::class, 'destroy']);
        //Participante
        Route::get('participantes', [ParticipanteController::class, 'index']);
        Route::get('participantes/causa/listar/{causaId}', [ParticipanteController::class, 'listadoPorCausa']);
        Route::post('participantes', [ParticipanteController::class, 'store']);
        Route::get('participantes/{participante}', [ParticipanteController::class, 'show']);
        Route::patch('participantes/{participante}', [ParticipanteController::class, 'update']);
        Route::patch('participantes/eliminar/{participante}', [ParticipanteController::class, 'destroy']);
        //Depositos
        Route::get('depositos', [DepositoController::class, 'index']);
        Route::post('depositos', [DepositoController::class, 'store']);
        Route::get('depositos/{deposito}', [DepositoController::class, 'show']);
        Route::patch('depositos/{deposito}', [DepositoController::class, 'update']);
        Route::patch('depositos/eliminar/{deposito}', [DepositoController::class, 'destroy']);
        //Devolucion Saldo
        Route::get('devolucion-saldo', [DevolucionSaldoController::class, 'index']);
        Route::post('devolucion-saldo', [DevolucionSaldoController::class, 'store']);
        Route::get('devolucion-saldo/{devolucionSaldo}', [DevolucionSaldoController::class, 'show']);
        Route::patch('devolucion-saldo/{devolucionSaldo}', [DevolucionSaldoController::class, 'update']);
        Route::patch('devolucion-saldo/eliminar/{devolucionSaldo}', [DevolucionSaldoController::class, 'destroy']);
        //Avance plantilla
        Route::get('avance-plantillas', [AvancePlantillaController::class, 'index']);
        Route::post('avance-plantillas', [AvancePlantillaController::class, 'store']);
        Route::get('avance-plantillas/listar/{avancePlantilla?}', [AvancePlantillaController::class, 'show']);
        Route::get('avance-plantillas/listarPorId/{idPlantilla}', [AvancePlantillaController::class, 'listarPlantillaPorId']);
        Route::patch('avance-plantillas/{avancePlantilla}', [AvancePlantillaController::class, 'update']);
        Route::patch('avance-plantillas/eliminar/{avancePlantilla}', [AvancePlantillaController::class, 'destroy']);
        //Posta
        Route::get('postas', [PostaController::class, 'index']);
        Route::post('postas', [PostaController::class, 'store']);
        Route::get('postas/listar/{posta?}', [PostaController::class, 'show']);
        Route::get('postas/listarPorId/{idPlantilla?}', [PostaController::class, 'listarPorIdPlantilla']);
        Route::patch('postas/{posta}', [PostaController::class, 'update']);
        Route::patch('postas/eliminar/{posta}', [PostaController::class, 'destroy']);
        //Agenda apunte
        Route::get('agenda-apuntes', [AgendaApunteController::class, 'index']);
        Route::post('agenda-apuntes', [AgendaApunteController::class, 'store']);
        Route::get('agenda-apuntes/{agendaApunte}', [AgendaApunteController::class, 'show']);
        Route::patch('agenda-apuntes/{agendaApunte}', [AgendaApunteController::class, 'update']);
        Route::patch('agenda-apuntes/eliminar/{agendaApunte}', [AgendaApunteController::class, 'destroy']);
        //Causa Posta
        Route::get('causa-postas', [CausaPostaController::class, 'index']);
        Route::post('causa-postas', [CausaPostaController::class, 'store']);
        Route::get('causa-postas/{causaPosta}', [CausaPostaController::class, 'show']);
        Route::patch('causa-postas/{causaPosta}', [CausaPostaController::class, 'update']);
        Route::patch('causa-postas/eliminar/{causaPosta}', [CausaPostaController::class, 'destroy']);
        //Tipo posta
        Route::get('tipo-postas', [TipoPostaController::class, 'index']);
        Route::post('tipo-postas', [TipoPostaController::class, 'store']);
        Route::get('tipo-postas/{tipoPosta}', [TipoPostaController::class, 'show']);
        Route::patch('tipo-postas/{tipoPosta}', [TipoPostaController::class, 'update']);
        Route::patch('tipo-postas/eliminar/{tipoPosta}', [TipoPostaController::class, 'destroy']);
        //Informe Posta
        Route::get('informe-postas', [InformePostaController::class, 'index']);
        Route::post('informe-postas', [InformePostaController::class, 'store']);
        Route::get('informe-postas/{informePosta}', [InformePostaController::class, 'show']);
        Route::patch('informe-postas/{informePosta}', [InformePostaController::class, 'update']);
        Route::patch('informe-postas/eliminar/{informePosta}', [InformePostaController::class, 'destroy']);
        //Matriz cotizacion
        Route::get('matriz-cotizacion', [MatrizCotizacionController::class, 'index']);
        Route::get('matriz-cotizacion/{matrizCotizacion}', [MatrizCotizacionController::class, 'show']);
        Route::patch('matriz-cotizacion/{matrizCotizacion}', [MatrizCotizacionController::class, 'update']);
        //Orden
        Route::get('orden', [OrdenController::class, 'index']);
        Route::get('orden/listar-por-causa/{id?}', [OrdenController::class, 'listarPorCausa']);
        Route::post('orden', [OrdenController::class, 'store']);
        Route::get('orden/{orden}', [OrdenController::class, 'show']);
        Route::patch('orden/{orden}', [OrdenController::class, 'update']);
        Route::patch('orden/eliminar/{orden}', [OrdenController::class, 'destroy']);
        Route::patch('orden/aceptar/{orden}', [OrdenController::class, 'aceptarOrden']);
        //Cotizacion

        //Presupuesto
        Route::get('presupuestos', [PresupuestoController::class, 'index']);
        Route::post('presupuestos', [PresupuestoController::class, 'store']);
        Route::get('presupuestos/{presupuesto}', [PresupuestoController::class, 'show']);
        Route::patch('presupuestos/{presupuesto}', [PresupuestoController::class, 'update']);
        Route::patch('presupuestos/eliminar/{presupuesto}', [PresupuestoController::class, 'destroy']);
        Route::patch('presupuestos/entregar/{presupuesto}', [PresupuestoController::class, 'entregarPresupuesto']);
        //Procuraduria Descarga
        Route::get('descargas', [ProcuraduriaDescargaController::class, 'index']);
        Route::post('descargas', [ProcuraduriaDescargaController::class, 'store']);

        //Confirmacion
        Route::patch('confirmacion/pronuncio-abogado/{confirmacion}', [ConfirmacionController::class, 'pronuncioAbogado']);
        Route::patch('confirmacion/pronuncio-contador/{confirmacion}', [ConfirmacionController::class, 'pronuncioContador']);
        //Gestion Alternativa
        Route::post('gestion-alternativa', [GestionAlternativaController::class, 'store']);
        Route::patch('gestion-alternativa/{gestionAlternativa}', [GestionAlternativaController::class, 'update']);
        Route::patch('gestion-alternativa/eliminar/{gestionAlternativa}', [GestionAlternativaController::class, 'destroy']);
        Route::get('gestion-alternativa/orden/{ordenId}', [GestionAlternativaController::class, 'obtenerPorOrdenId']);
        //Paquetes
        Route::get('paquetes', [PaqueteController::class, 'index']);
        Route::post('paquetes', [PaqueteController::class, 'store']);
        Route::patch('paquetes/{paquete}', [PaqueteController::class, 'update']);
        Route::patch('paquetes/eliminar/{paquete}', [PaqueteController::class, 'destroy']);
        Route::get('paquetes/{paquete}', [PaqueteController::class, 'show']);
        //Compra paquetes
        Route::post('compra-paquetes', [CompraPaqueteController::class, 'store']);
        Route::get('compra-paquetes', [CompraPaqueteController::class, 'index']);
        Route::get('compra-paquetes/{compraPaquete}', [CompraPaqueteController::class, 'show']);
        //Usuarios
        Route::get('usuarios/abogados', [UserController::class, 'listarAbogados']);
        Route::get('usuarios/procuradores', [UserController::class, 'listarProcuradores']);
        Route::get('usuarios/listar/{user?}', [UserController::class, 'show']);
        //Documentos Categorias
        Route::get('documentos-categorias', [DocumentosCategoriaController::class, 'index']);
        Route::get('documentos-categorias/tramites', [DocumentosCategoriaController::class, 'indexTramites']);
        Route::get('documentos-categorias/normas', [DocumentosCategoriaController::class, 'indexNormas']);
        Route::post('documentos-categorias', [DocumentosCategoriaController::class, 'store']);
        Route::get('documentos-categorias/listar/{documentosCategoria?}', [DocumentosCategoriaController::class, 'show']);
        Route::patch('documentos-categorias/{documentosCategoria}', [DocumentosCategoriaController::class, 'update']);
        Route::patch('documentos-categorias/eliminar/{documentosCategoria}', [DocumentosCategoriaController::class, 'destroy']);
        Route::get('documentos-categorias/subcategoria/listado/{documentosCategoria}', [DocumentosCategoriaController::class, 'listarSubcategorias']);
        Route::get('documentos-categorias/categoria/listado', [DocumentosCategoriaController::class, 'listarCategorias']);

        Route::get('documentos-categorias/tramites/listado', [DocumentosCategoriaController::class, 'listarCategoriasTramites']);
        Route::get('documentos-categorias/normas/listado', [DocumentosCategoriaController::class, 'listarCategoriasNormas']);
        //Documentos - NORMAS-TRAMITES
        Route::get('documentos', [DocumentoController::class, 'index']);
        Route::get('documentos/tramites', [DocumentoController::class, 'indexTramites']);
        Route::get('documentos/normas', [DocumentoController::class, 'indexNormas']);
        Route::post('documentos', [DocumentoController::class, 'store']);
        Route::post('documentos/{documento}', [DocumentoController::class, 'update']);
        Route::patch('documentos/eliminar/{documento}', [DocumentoController::class, 'destroy']);
    });
});
