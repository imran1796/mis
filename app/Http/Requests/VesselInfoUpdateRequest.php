<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VesselInfoUpdateRequest extends FormRequest
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
            'rotation_no'        => 'nullable|string|max:50',
            'jetty'              => 'nullable|string|max:50',
            'operator'           => 'nullable|string|max:50',
            'local_agent'        => 'nullable|string|max:50',
            'effective_capacity' => 'nullable',
            'arrival_date'       => 'nullable|date',
            'arrival_time'       => 'nullable|date_format:H:i:s',
            'berth_date'         => 'nullable|date',
            'berth_time'         => 'nullable|date_format:H:i:s',
            'sail_date'          => 'nullable|date',
            'sail_time'          => 'nullable|date_format:H:i:s',
        ];
    }
}
