<?php

namespace App\Http\Requests\User;

use App\Models\User\Agent;
use App\Models\User\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class handles the validation of agent save requests.
 */
class AgentSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();
        $agent = $this->getAgent();

        $rules = [
            'first_name' => ['required', 'string', 'max:127'],
            'last_name' => ['required', 'string', 'max:127'],
            'position' => ['required', 'string', 'max:127'],
            'email' => $this->getEmailRules($agent, $user),
            'role' => [
                'required',
                'string',
                'max:15',
                Rule::in(array_keys(config('static_data.roles'))),
            ],

        ];
        // dd($rules);
        return $rules;
    }

    /** Retrieve the agent from the route parameters. */
    protected function getAgent(): ?Agent
    {
        $agent = $this->route('agent');
        return $agent ? $agent : null;
    }

    /** Get the validation rules for the email field. */
    protected function getEmailRules(?Agent $agent, User $user): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('agents', 'email')
                ->where(fn(Builder $query) => $query->where('user_id', $user->id))
                ->ignore($agent),
        ];
    }
}
