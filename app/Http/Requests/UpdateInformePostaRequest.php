<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformePostaRequest extends FormRequest
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
        return [
            'foja_informe'=>['sometimes','string','max:20'],
            'fecha_informe'=>['sometimes','date_format:Y-m-d H:i:s'],
            'calculo_gasto'=>['sometimes','numeric'],
            'honorario_informe'=>['sometimes','string'],

            'foja_truncamiento'=>['sometimes','string','max:20'],
            'honorario_informe_truncamiento'=>['sometimes','string'],
            'tipoposta_id'=>['sometimes'],
            'causaposta_id'=>['sometimes']
        ];
    }
}
