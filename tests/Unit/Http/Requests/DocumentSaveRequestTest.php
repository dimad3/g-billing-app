<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Document\DocumentSaveRequest;
use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use App\Models\User\Agent;
use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocumentSaveRequestTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected array $baseData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Auth::login($this->user);

        ($this->baseData = $this->getValidBaseData());
    }

    #[Test]
    public function it_validates_successfully_with_correct_data()
    {
        $response = $this->post(route('cabinet.documents.store'), $this->baseData);

        $response->assertRedirect(route('cabinet.documents.index'))
            ->assertSessionHas('success', 'Document created successfully.')
            ->assertValid();

        $this->assertDatabaseHas('documents', [
            'client_id' => $this->baseData['client_id'],
            'number' => $this->baseData['number'],
        ]);

        $this->assertDatabaseHas('document_items', [
            'document_id' => Document::where('client_id', $this->baseData['client_id'])
                ->where('number', $this->baseData['number'])
                ->first()->id,
        ]);
    }

    #[Test]
    public function it_fails_when_unique_value_is_not_unique()
    {
        $uniqueInputsToTest = ['number'];
        $this->baseData['number'] = 'unique-number';
        $this->post(route('cabinet.documents.store'), $this->baseData);

        $this->baseData['number'] = 'unique-number';
        $response = $this->post(route('cabinet.documents.store'), $this->baseData);
        $response->assertSessionHasErrors($uniqueInputsToTest)
            ->assertInvalid($uniqueInputsToTest);
    }

    #[Test]
    public function test_due_date_and_delivery_date_must_be_after_or_equal_to_document_date()
    {
        $this->baseData['document_date'] = '2025-04-01';
        $this->baseData['due_date'] = '2025-04-01'; // Valid: due_date is equal to document_date
        $this->baseData['delivery_date'] = '2025-04-02'; // Valid: delivery_date is after document_date

        $rules = (new DocumentSaveRequest())->rules();
        $validator = Validator::make($this->baseData, $rules);

        $this->assertFalse($validator->fails(), 'Validation should pass when dates are correct.');
    }

    #[Test]
    public function test_due_date_and_delivery_date_cannot_be_before_document_date()
    {
        $this->baseData['document_date'] = '2025-04-03';
        $this->baseData['due_date'] = '2025-04-02'; // Invalid: due_date is before document_date
        $this->baseData['delivery_date'] = '2025-04-01'; // Invalid: delivery_date is before document_date

        $rules = (new DocumentSaveRequest())->rules();
        $validator = Validator::make($this->baseData, $rules);

        $this->assertTrue($validator->fails(), 'Validation should fail when dates are before document_date.');
        $this->assertArrayHasKey('due_date', $validator->errors()->toArray());
        $this->assertArrayHasKey('delivery_date', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_all_inputs_successfully()
    {
        $inputsToTest = [
            'client_id' => [null, '', 'string', 9999999999],
            'document_date' => [null, '', 'string'],
            'number' => [null, '', [1, 2, 3], Str::random(32)],
            'document_type' => [null, '', [1, 2, 3], Str::random(32), 'invalid_document_type'],
            'advance_paid' => [null, '', 'string', -0.01, 999999999.01],
            'due_date' => [null, '', 'string'],
            'delivery_date' => ['string'],
            'status' => [null, '', [1, 2, 3], Str::random(16), 'invalid_status'],
            'transaction_description' => [[1, 2, 3], Str::random(128)],
            'tax_note' => [[1, 2, 3], Str::random(128)],
            'document_note' => [[1, 2, 3], Str::random(512)],
            'agent_id' => ['string', 9999999999],
            'show_created_by' => [null, '', 'string', 2, -1],
            'show_signature' => [null, '', 'string', 2, -1],
            'delivery_address' => [[1, 2, 3], Str::random(256)],
            'receiving_address' => [[1, 2, 3], Str::random(256)],

            'items' => [null, '', 3, 'string', Str::random(12)],
            'items.0.name' => [123, [1, 2, 3], Str::random(128)],
            'items.0.unit' => [123, [1, 2, 3], Str::random(16)],
            'items.0.quantity' => [null, 'string', 1.1234, -999999999.01, 999999999.01],
            'items.0.price' => ['', [1, 2, 3], 1.123456, -999999999.01, 999999999.01],
            'items.0.discount_rate' => [null, 'string', 1.123, -0.001, 100.001],
            'items.0.tax_rate' => ['', [1, 2, 3], 1.123, -0.001, 100.001],
            'items.1.name' => [123, [1, 2, 3], Str::random(128)],
            'items.1.unit' => [123, [1, 2, 3], Str::random(16)],
            'items.1.quantity' => [null, 'string', 1.1234, -999999999.01, 999999999.01],
            'items.1.price' => ['', [1, 2, 3], 1.123456, -999999999.01, 999999999.01],
            'items.1.discount_rate' => [null, 'string', 1.123, -0.001, 100.001],
            'items.1.tax_rate' => ['', [1, 2, 3],1.123, -0.001, 100.001],
        ];

        foreach ($inputsToTest as $input => $invalidValues) {
            foreach ($invalidValues as $invalidValue) {
                $this->checkIfValidationFails($input, $invalidValue);
            }
        }
    }

    protected function checkIfValidationFails(string $input, mixed $invalidValue): void
    {
        ($data = $this->getModifiedData($input, $invalidValue));
        // dump([$input, $data]);
        $response = $this->post(route('cabinet.documents.store'), $data);
        // dump(session()->get('errors'));
        $response->assertSessionHasErrors($input)
            ->assertInvalid($input);
        $invalidValueAsString =  is_string($invalidValue) ? $invalidValue : json_encode($invalidValue);
        dump("$input: $invalidValueAsString", session()->get('errors')?->toArray());
    }

    protected function getModifiedData(string $input, mixed $value): array
    {
        $data = $this->baseData;
        data_set($data, $input, $value);

        return ($data);
    }

    /** Generate valid base data for a Document instance. */
    protected function getValidBaseData(): array
    {
        $client = Client::factory()->for($this->user)->create();
        $agent = Agent::factory()->for($this->user)->create();

        $document = Document::factory()->make([
            'client_id' => $client->id,
            'agent_id' => $agent->id,
        ]);

        // Generate document items
        $items = DocumentItem::factory()->count(3)->make(); // using make() instead of create() prevent immediate persistence

        // Convert document items to an array and attach to the document data
        return (array_merge($document->toArray(), [
            'items' => $items->toArray(),
        ]));
    }
}
