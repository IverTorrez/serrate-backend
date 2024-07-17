<?php
namespace App\Services;

use App\Constants\Estado;
use App\Models\Orden;
use Illuminate\Http\Request;

class OrdenService
{
    public function update($data, $ordenId)
    {
        $orden = Orden::findOrFail($ordenId);
        $orden->update($data);
        return $orden;
    }

}
