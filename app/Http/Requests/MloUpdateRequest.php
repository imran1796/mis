<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MloUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $mloId = $this->route('mlo'); // or use `$this->mlo->id` if route-model binding is used

        return [
            'line_belongs_to' => [
                'string',
                'max:50',
            ],
            'mlo_details' => [
                'nullable',
                'string',
            ],
            'effective_from' => [
                'nullable',
                'date',
            ],
            'effective_to' => [
                'nullable',
                'date',
            ],
            'mlo_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mlos')
                    ->ignore($mloId)
                    ->where(function ($query) {
                        return $query->where('line_belongs_to', $this->line_belongs_to);
                    }),
            ],
        ];
    }
}
