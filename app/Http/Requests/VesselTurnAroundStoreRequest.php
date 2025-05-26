<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class VesselTurnAroundStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $date = $this->input('date');

            $exists = \DB::table('vessel_turn_arounds')
                ->whereDate('date', Carbon::createFromFormat('M-Y', $date)->startOfMonth())
                ->exists();

            if ($exists) {
                $validator->errors()->add('file', 'Data already exists for the selected date.');
            }
        });
    }
}
