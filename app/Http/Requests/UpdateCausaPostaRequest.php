<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCausaPostaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        //por PATCH
        return [
            'nombre'=>['sometimes','string'],
            'numero_posta'=>['sometimes','numeric'],
            'copia_nombre_plantilla'=>['sometimes','string','max:300'],
            'causa_id'=>['sometimes'],
          ];
    }
}
