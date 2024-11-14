<?php

namespace App\Http\Requests;

use App\Constants\TipoUsuario;
use Illuminate\Foundation\Http\FormRequest;

class StoreAuthRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'tipo' => 'required|string|in:' . implode(',', TipoUsuario::getValues()),

            'persona.nombre' => 'required|string|max:255',
            'persona.apellido' => 'required|string|max:255',
            'persona.telefono' => 'nullable|string|max:20',
            'persona.direccion' => 'nullable|string|max:255',
            //'persona.coordenadas' => 'nullable|string|max:255',
            //'persona.observacion' => 'nullable|string',
            //'persona.foto_url' => 'nullable|url',
            //'persona.estado' => 'nullable|boolean',
            //'persona.es_eliminado' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'El campo nombre es obligatorio.',
            'email.required' => 'El campo email es obligatorio.',
            'email.email' => 'El campo email debe ser una dirección de correo válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'tipo.required' => 'El campo tipo es obligatorio.',
            'tipo.in' => 'El tipo seleccionado no es válido.',

            'persona.nombre.required' => 'El campo nombre de la persona es obligatorio.',
            'persona.apellido.required' => 'El campo apellido es obligatorio.',
            'persona.telefono.max' => 'El teléfono no debe exceder los 20 caracteres.',
            'persona.direccion.max' => 'La dirección no debe exceder los 255 caracteres.',
            // 'persona.coordenadas.max' => 'Las coordenadas no deben exceder los 255 caracteres.',
            // 'persona.foto_url.url' => 'La URL de la foto debe ser válida.',
            // 'persona.estado.boolean' => 'El estado debe ser verdadero o falso.',
            // 'persona.es_eliminado.boolean' => 'El campo "es eliminado" debe ser verdadero o falso.',
        ];
    }
}
