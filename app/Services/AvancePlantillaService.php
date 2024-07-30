<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\AvancePlantilla;
use Illuminate\Http\Request;

class AvancePlantillaService
{
    public function update($data, $plantillaId)
    {
        $avancePlantilla = AvancePlantilla::findOrFail($plantillaId);
        $avancePlantilla->update($data);
        return $avancePlantilla;
    }
    public function obtenerUno($plantillaId)
    {
        $avancePlantillaId = AvancePlantilla::findOrFail($plantillaId);
        return $avancePlantillaId;
    }

}
