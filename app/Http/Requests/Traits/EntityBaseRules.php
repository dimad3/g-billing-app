<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

/**
 * Trait provides base validation rules for entity-related requests.
 */
trait EntityBaseRules
{
    /** Get the base validation rules for entities. */
    protected function entityBaseRules(?string $entityType = null): array
    {
        $rules = [
            'entity_type' => [
                'required',
                'string',
                'max:15',
                Rule::in(array_keys(config('static_data.entity_types'))),
            ],
            'legal_form' => ['required', 'string', 'max:31'],
            'address' => ['nullable', 'string', 'max:63'],
            'city' => ['nullable', 'string', 'max:31'],
            'country' => ['nullable', 'string', 'max:31'],
            'postal_code' => ['nullable', 'string', 'max:15'],
            'note' => ['nullable', 'string', 'max:511'],
            'bank_accounts' => ['array'],
            'bank_accounts.*.bank_id' => ['required', 'exists:banks,id'],
            'bank_accounts.*.bank_account' => ['required', 'string', 'max:31', 'distinct'],
        ];

        $this->addEntitySpecificRules($rules, $entityType);

        return $rules;
    }

    /** Add entity-specific validation rules based on the entity type. */
    protected function addEntitySpecificRules(array &$rules, ?string $entityType = null): void
    {
        if ($entityType === 'legal_entity') {
            $rules['legal_form'][] = Rule::in(array_keys(config('static_data.legal_forms.legal_entity')));
            $rules['name'] = ['required', 'string', 'max:127'];
        } elseif ($entityType === 'individual') {
            $rules['legal_form'][] = Rule::in(array_keys(config('static_data.legal_forms.individual')));
            $rules['first_name'] = ['required', 'string', 'max:127'];
            $rules['last_name'] = ['required', 'string', 'max:127'];
        }
    }
}
