<?php

namespace App\Http\Requests\Document;

use App\Models\Document\Document;
use App\Models\User\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class handles the validation of agent save requests.
 */
class DocumentSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // dd($this->all());
        $user = auth()->user();
        $document = $this->getDocument();

        return ([
            'client_id' => $this->getClientIdRules($user),
            'document_date' => ['required', 'date'],
            'number' => $this->getDocumNumberRules($user, $document),
            'document_type' => ['required', 'string', 'max:31', Rule::in(array_keys(config('static_data.document_types')))],
            'advance_paid' => ['required', 'decimal:0,2', 'between:0,999999999'],
            'due_date' => ['required', 'date', 'after_or_equal:document_date',],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:document_date',],
            'status' => ['required', 'string', 'max:15', Rule::in(array_keys(config('static_data.document_statuses')))],
            'transaction_description' => ['nullable', 'string', 'max:127'],
            'tax_note' => ['nullable', 'string', 'max:127'],
            'document_note' => ['nullable', 'string', 'max:511'],
            'agent_id' => $this->getAgentIdRules($user),
            'show_created_by' => ['required', 'boolean'],
            'show_signature' => ['required', 'boolean'],
            'delivery_address' => ['nullable', 'string', 'max:255'], // Only for specific document types
            'receiving_address' => ['nullable', 'string', 'max:255'], // Only for specific document types

            'items' => ['required', 'array'],
            'items.*.name' => ['nullable', 'string', 'max:127',],
            'items.*.unit' => ['nullable', 'string', 'max:15'],
            'items.*.quantity' => ['required', 'decimal:0,3', 'between:-999999999,999999999'],
            'items.*.price' => ['required', 'decimal:0,5', 'between:-999999999,999999999'],
            'items.*.discount_rate' => ['required', 'decimal:0,2', 'between:0,100'],
            'items.*.tax_rate' => ['required', 'decimal:0,2', 'between:0,100'],
        ]);
    }

    /** Retrieve the document from the route parameters. */
    protected function getDocument(): ?Document
    {
        $document = $this->route('document');

        return $document ? $document->loadMissing(['user']) : null;
    }

    /** Get the validation rules for the client ID field. */
    protected function getClientIdRules(User $user): array
    {
        return [
            'required',
            'integer',
            Rule::exists('clients', 'id')->where(function (Builder $query) use ($user) {
                return $query
                    ->where('user_id', $user->id);
            }),
        ];
    }

    /** Get the validation rules for the agent ID field. */
    protected function getAgentIdRules(User $user): array
    {
        return [
            'nullable',
            'integer',
            Rule::exists('agents', 'id')->where(function (Builder $query) use ($user) {
                return $query
                    ->where('user_id', $user->id);
            }),
        ];
    }

    /** Get the validation rules for the document number field. */
    protected function getDocumNumberRules(User $user, ?Document $document): array
    {
        return [
            'required',
            'string',
            'max:31',
            Rule::unique('documents')->where(function (Builder $query) use ($user) {
                return $query->where('user_id', $user->id);
            })->ignore($document),
        ];
    }
}
