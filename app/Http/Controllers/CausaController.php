<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Causa;
use App\Enums\MessageHttp;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Constants\EstadoCausa;
use App\Constants\TipoUsuario;
use App\Services\CausaService;
use App\Services\PostaService;
use Illuminate\Support\Facades\DB;
use App\Services\CausaPostaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\CausaCollection;
use App\Http\Requests\StoreCausaRequest;
use App\Services\AvancePlantillaService;
use App\Http\Requests\UpdateCausaRequest;
use App\Models\TipoLegal;

class CausaController extends Controller
{
    protected $causaService;
    protected $avancePlantillaService;
    protected $postaService;
    protected $causaPostaService;
    protected $userService;

    public function __construct(
        CausaService $causaService,
        AvancePlantillaService $avancePlantillaService,
        PostaService $postaService,
        CausaPostaService $causaPostaService,
        UserService $userService
    ) {
        $this->causaService = $causaService;
        $this->avancePlantillaService = $avancePlantillaService;
        $this->postaService = $postaService;
        $this->causaPostaService = $causaPostaService;
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Causa::active();

        // Manejo de búsqueda
        if ($request->has('search')) {
            $search = json_decode($request->input('search'), true);
            $query->search($search);
        }

        // Manejo de ordenamiento
        if ($request->has('sort')) {
            $sort = json_decode($request->input('sort'), true);
            $query->sort($sort);
        }

        $perPage = $request->input('perPage', 10);
        $causas = $query->paginate($perPage);

        $causas->load('materia');
        $causas->load('tipoLegal');
        $causas->load('categoria');
        $causas->load('abogado.persona');
        $causas->load('procurador.persona');
        return new CausaCollection($causas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCausaRequest $request)
    {
        DB::beginTransaction();
        try {
            $usuarioPmaestro = $this->userService->obtenerUnPMaestro();
            $idUser = Auth::user()->id;

            if (Auth::user()->tipo === TipoUsuario::ABOGADO_LIDER) {
                $procuradorId = $request->procurador_id;
                $abogadoId = $request->abogado_id;
            } else {
                $procuradorId = $usuarioPmaestro->id;
                $abogadoId = $idUser;
            }

            $data = [
                'nombre' => $request->nombre,
                'observacion' => $request->observacion,
                'objetivos' => '',
                'estrategia' => '',
                'informacion' => '',
                'apuntes_juridicos' => '',
                'apuntes_honorarios' => '',
                'tiene_billetera' => $request->tiene_billetera,
                'billetera' => 0,
                'saldo_devuelto' => 0,
                'color' => $request->color,
                'materia_id' => $request->materia_id,
                'tipolegal_id' => $request->tipolegal_id,
                'categoria_id' => $request->categoria_id,
                'abogado_id' => $abogadoId,
                'procurador_id' => $procuradorId,
                'usuario_id' => $idUser,
            ];
            $data['plantilla_id'] = $request->has('plantilla_id') ? $request->plantilla_id : 0;
            $causa = $this->causaService->store($data);

            //SI ELIGIO UNA PLANTILLA DE AVANCE SE REGISTRA EN LA TABLA causa_postas
            if ($request->has('plantilla_id') && $request->plantilla_id > 0) {
                $plantilla_id = $request->plantilla_id;
                $causaPosta = $this->guardarCausaPosta($causa->id, $plantilla_id);
            }

            DB::commit();
            return response()->json([
                'message' => MessageHttp::CREADO_CORRECTAMENTE,
                'data' => $causa
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error registrar causa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error registrar causa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Causa $causa)
    {
        $causa = $this->causaService->obtenerUno($causa->id);
        $data = [
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $causa
        ];
        return response()->json($data);
    }
    public function listarActivos()
    {
        $causas = $this->causaService->listarActivos();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Causa $causa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCausaRequest $request, Causa $causa)
    {
        DB::beginTransaction();
        try {
            $data = $request->only([
                'nombre',
                'observacion',
                'objetivos',
                'estrategia',
                'informacion',
                'apuntes_juridicos',
                'apuntes_honorarios',

                'color',
                'materia_id',
                'tipolegal_id',
                'categoria_id',
                'abogado_id',
                'procurador_id',
                'estado',
                'es_eliminado'
            ]);
            $plantillaId = $request->plantilla_id;
            //Pregunta si plantilla_id que esta en el request es diferente al valor de plantilla_id de la causa (si eligio otra plantilla)
            if ($request->has('plantilla_id') && $plantillaId > 0 && $plantillaId != $causa->plantilla_id) {
                $data['plantilla_id'] = $plantillaId;
                if ($causa->plantilla_id > 0) {
                    $this->causaPostaService->eliminarPorCausaId($causa->id);
                }
                $causaPosta = $this->guardarCausaPosta($causa->id, $plantillaId);
            }
            $causa = $this->causaService->update($data, $causa->id);

            DB::commit();
            return response()->json([
                'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
                'data' => $causa
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error actualizado causa: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error actualizado causa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Causa $causa)
    {
        $causa->es_eliminado = 1;
        $causa->save();
        $data = [
            'message' => MessageHttp::ELIMINADO_CORRECTAMENTE,
            'data' => $causa
        ];
        return response()->json($data);
    }
    public function guardarCausaPosta($causaId, $pantillaId)
    {
        $avancePlantilla = $this->avancePlantillaService->obtenerUno($pantillaId);
        //Causa Posta cero, (INICIO)
        $dataCausaPostaInicio = [
            'nombre' => 'INICIO',
            'numero_posta' => 0,
            'copia_nombre_plantilla' => $avancePlantilla->nombre,
            'tiene_informe' => 0,
            'causa_id' => $causaId,
        ];
        $causaPosta = $this->causaPostaService->store($dataCausaPostaInicio);

        $postas = $this->postaService->listarPorAvancePlantillaId($avancePlantilla->id);
        foreach ($postas as $posta) {
            $data = [
                'nombre' => $posta->nombre,
                'numero_posta' => $posta->numero_posta,
                'copia_nombre_plantilla' => $avancePlantilla->nombre,
                'tiene_informe' => 0,
                'causa_id' => $causaId,
            ];
            // Llama a la función store de CausaPostaService
            $causaPosta = $this->causaPostaService->store($data);
        }
        return $causaPosta;
    }
    public function listarCausasParaPaquete()
    {
        $causas = $this->causaService->listarCausasParaPaquete();
        $data = [
            'message' => MessageHttp::OBTENIDOS_CORRECTAMENTE,
            'data' => $causas
        ];
        return response()->json($data);
    }

}
