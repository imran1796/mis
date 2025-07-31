<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VesselInfoCreateRequest extends FormRequest
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
            'route_id' => ['required', 'string'],
            'date' => ['required', 'date'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $routeId = $this->input('route_id');
            $date = $this->input('date');

            $exists = \DB::table('vessel_infos')
                ->where('route_id', $routeId)
                ->whereDate('date', Carbon::createFromFormat('d-M-Y', '01-' . $date)->startOfMonth())
                ->exists();

            if ($exists) {
                $validator->errors()->add('file', 'Data already exists for the selected route.');
            }
        });
    }
}
