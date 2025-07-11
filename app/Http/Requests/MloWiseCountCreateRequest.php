
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class MloWiseCountCreateRequest extends FormRequest
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

            $exists = \DB::table('mlo_wise_counts')
                ->where('route_id', $routeId)
                ->whereDate('date', Carbon::createFromFormat('d-M-Y', '01-' . $date)->startOfMonth())
                ->exists();

            if ($exists) {
                $validator->errors()->add('file', 'Data already exists for the selected route.');
            }
        });
    }
}
