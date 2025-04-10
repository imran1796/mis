<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MloUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
        ];
    }
}
