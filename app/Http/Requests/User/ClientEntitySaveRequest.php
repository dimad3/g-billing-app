<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\EntityBaseRules;
use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class handles the validation of client entity save requests.
 */
class ClientEntitySaveRequest extends FormRequest
{
    use EntityBaseRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();
        $client = $this->getClient();
        $entity = $client?->entity;
        $userClientsIds = $this->getUserClientsIds($user); // Get the IDs of the user's clients

        $entityType = $this->getEntityType();
        $baseRules = $this->entityBaseRules($entityType); // Get base validation rules for entities

        // Define client-specific validation rules
        $clientRules = [
            // client rules
            'email' => $this->getEmailRules($client, $user),
            'due_days' => ['required', 'integer', 'min:0', 'max:365'],
            'discount_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            // entity-specific rules that need client context
            'id_number' => $this->getIdNumberRules($entity, $userClientsIds),
            'vat_number' => $this->getVatNumberRules($entity, $userClientsIds),
        ];

        return array_merge($baseRules, $clientRules);
    }

    /** Retrieve the client from the route parameters. */
    protected function getClient(): ?Client
    {
        $client = $this->route('client');

        return $client ? $client->loadMissing(['user', 'entity']) : null;
    }

    /** Get the entity type from the request input. */
    protected function getEntityType(): ?string
    {
        $entityType = $this->input('entity_type');

        return is_string($entityType) ? $entityType : null;
    }

    /** Get the IDs of the clients associated with the user. */
    protected function getUserClientsIds(User $user): array
    {
        return $user->clients()->pluck('id')->toArray();
    }

    /** Get the validation rules for the email field. */
    protected function getEmailRules(?Client $client, User $user): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('clients', 'email')
                ->where(fn(Builder $query) => $query->where('user_id', $user->id))
                ->ignore($client),
        ];
    }

    /** Get the validation rules for the ID number field. */
    protected function getIdNumberRules(?Entity $entity, array $userClientsIds): array
    {
        return [
            'required',
            'string',
            'max:11',
            Rule::unique('entities', 'id_number')->where(function (Builder $query) use ($userClientsIds) {
                return $query
                    ->whereIn('entityable_id', $userClientsIds)
                    ->where('entityable_type', strtolower(basename(Client::class)));
            })->ignore($entity),
        ];
    }

    /** Get the validation rules for the VAT number field. */
    protected function getVatNumberRules(?Entity $entity, array $userClientsIds): array
    {
        return [
            'nullable',
            'string',
            'max:13',
            Rule::unique('entities', 'vat_number')->where(function (Builder $query) use ($userClientsIds) {
                return $query
                    ->whereIn('entityable_id', $userClientsIds)
                    ->where('entityable_type', strtolower(basename(Client::class)));
            })->ignore($entity),
        ];
    }
}
