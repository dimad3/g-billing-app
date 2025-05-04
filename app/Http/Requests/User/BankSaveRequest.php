<?php

namespace App\Http\Requests\User;

use App\Models\User\Bank;
use App\Models\User\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class handles the validation of bank save requests.
 */
class BankSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();
        $bank = $this->getBank();

        $rules = [
            'name' => ['required', 'string', 'max:32'],
            'bank_code' => $this->getBankCodeRules($bank, $user),
        ];
        // dd($rules);
        return $rules;
    }

    /** Retrieve the bank from the route parameters. */
    protected function getBank(): ?Bank
    {
        $bank = $this->route('bank');
        return $bank ? $bank : null;
    }

    /** Get the validation rules for the email field. */
    protected function getBankCodeRules(?Bank $bank, User $user): array
    {
        return [
            'required',
            'string',
            'max:8',
            Rule::unique('banks', 'bank_code')
                ->where(fn(Builder $query) => $query->where('user_id', $user->id))
                ->ignore($bank),
        ];
    }
}
