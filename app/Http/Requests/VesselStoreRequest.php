<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VesselStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'vessel_name' => [
                'required',
                'string',
                Rule::unique('vessels')->where(function ($query) {
                    return $query->where('imo_no', $this->imo_no);
                }),
            ],
            'length_overall' => [
                'required',
                'numeric',
                'between:0,999999.99'
            ],
            'crane_status' => [
                'required',
                Rule::in(['G', 'GL']),
            ],
            'nominal_capacity' => [
                'required',
                'integer',
                'min:0',
            ],
            'imo_no' => [
                'required',
                'numeric',
            ],
        ];
    }
}
