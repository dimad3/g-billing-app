<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class handles the validation of settings save requests.
 */
class DocumentSettingSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        ($userAgentsIds = auth()->user()->agents()->pluck('id')->toArray());

        $rules = [
            'number_prefix' => ['required', 'string', 'max:15'],
            'next_number' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'default_agent_id' => ['required', 'integer', Rule::in($userAgentsIds),],
            'default_tax_rate' => ['required', 'numeric', 'between:0,100'],
        ];
        // dd($rules);
        return $rules;
    }
}
