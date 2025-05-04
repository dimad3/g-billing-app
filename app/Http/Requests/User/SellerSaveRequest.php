<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\EntityBaseRules;
use App\Models\Entity\Entity;
use App\Models\User\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class Handles the validation of seller entity save requests.
 */
class SellerSaveRequest extends FormRequest
{
    use EntityBaseRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $entityType = $this->getEntityType();
        $baseRules = $this->entityBaseRules($entityType); // Get base validation rules for entities

        // Define seller-specific validation rules
        $sellerRules = [
            // The unique rule is not applied because theoretically,
            // more than one user can create invoices with the same seller requisites
            'id_number' => ['required','string','max:11',],
            'vat_number' => ['nullable','string','max:13',],
        ];

        $rules = array_merge($baseRules, $sellerRules);

        return $rules;
    }

    /** Get the entity type from the request input. */
    protected function getEntityType(): ?string
    {
        $entityType = $this->input('entity_type');

        return is_string($entityType) ? $entityType : null;
    }
}
