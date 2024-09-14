<?php

namespace App\Http\Controllers;

use App\Enums\MessageHttp;
use App\Models\TablaConfig;
use Illuminate\Http\Request;
use App\Services\TablaConfigService;
use App\Http\Requests\UpdateTablaConfigRequest;

class TablaConfigController extends Controller
{
    protected $tablaConfigService;
    public function __construct(TablaConfigService $tablaConfigService)
    {
        $this->tablaConfigService = $tablaConfigService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $tablaConfig = $this->tablaConfigService->mostarDatosTablaConfig();
        return response()->json([
            'message' => MessageHttp::OBTENIDO_CORRECTAMENTE,
            'data' => $tablaConfig
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TablaConfig $tablaConfig)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTablaConfigRequest $request)
    {
        $data = $request->only([
            'titulo_index',
            'texto_index'
        ]);
        if ($request->hasFile('imagen_index')) {
            $file = $request->file('imagen_index');
            $pathindex = $file->store('uploads/img/config', 'public');
            $data['imagen_index'] = $pathindex;
        }
        if ($request->hasFile('imagen_logo')) {
            $file = $request->file('imagen_logo');
            $pathlogo = $file->store('uploads/img/config', 'public');
            $data['imagen_logo'] = $pathlogo;
        }
        $tablaConfig = $this->tablaConfigService->update($data, 1);
        return response()->json([
            'message' => MessageHttp::ACTUALIZADO_CORRECTAMENTE,
            'data' => $tablaConfig
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TablaConfig $tablaConfig)
    {
        //
    }
}
