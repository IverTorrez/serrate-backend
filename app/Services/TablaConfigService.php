<?php

namespace App\Services;

use App\Constants\Estado;
use App\Models\TablaConfig;
use Illuminate\Http\Request;

class TablaConfigService
{
    public function update($data, $tablaConfigId)
    {
        $tablaConfig = TablaConfig::findOrFail($tablaConfigId);
        $tablaConfig->update($data);
        return $tablaConfig;
    }
    public function mostarDatosTablaConfig()
    {
        $tablaConfig = TablaConfig::findOrFail(1);
        return $tablaConfig;
    }
}
