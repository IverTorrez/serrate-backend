<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'persona.nombre' => 'sometimes|string|max:255',
            'persona.apellido' => 'sometimes|string|max:255',
            'persona.telefono' => 'sometimes|string|max:15',
            'persona.direccion' => 'sometimes|string|max:255',
            'persona.observacion' => 'sometimes|string|max:255',

            // 'nombre' => 'required|string|max:255',
            // 'apellido' => 'required|string|max:255',
            // 'telefono' => 'nullable|string|max:20',
            // 'direccion' => 'nullable|string|max:255',
            // 'observacion' => 'nullable|string',
            // 'foto_url' => 'nullable|url',
        ];
    }
}
