<?php

namespace Tests\Unit\Http\Requests;

use App\Models\User\Bank;
use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientEntitySaveRequestTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Bank $bank;
    protected array $baseData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Auth::login($this->user);

        Bank::truncate();
        $this->bank = Bank::factory()->create(['name' => 'Test Bank ' . uniqid()]);

        $this->baseData = $this->getValidBaseData();
    }

    #[Test]
    public function it_validates_successfully_with_correct_data()
    {
        $response = $this->post(route('cabinet.clients.store'), $this->baseData);

        $response->assertRedirect(route('cabinet.clients.index'))
            ->assertSessionHas('success', 'Client created successfully.')
            ->assertValid();

        $this->assertDatabaseHas('clients', [
            'email' => $this->baseData['email'],
        ]);

        $client = Client::where('email', $this->baseData['email'])->first()->loadMissing('entity');

        $this->assertDatabaseHas('entities', [
            'entityable_id' => $client->id,
            'name' => $this->baseData['name'],
        ]);

        $this->assertCount(2, $client->entity->bankAccounts);
    }

    #[Test]
    public function it_fails_when_required_fields_are_missing_for_legal_entity()
    {
        $this->baseData['legal_form'] = null;
        $this->baseData['name'] = null;

        $response = $this->post(route('cabinet.clients.store'), $this->baseData);
        $response->assertSessionHasErrors(['legal_form', 'name'])
            ->assertInvalid(['legal_form', 'name']);
    }

    #[Test]
    public function it_fails_when_required_fields_are_missing_for_individual()
    {
        $this->baseData['entity_type'] = 'individual';
        $this->baseData['legal_form'] = null;
        $this->baseData['first_name'] = null;
        $this->baseData['last_name'] = null;

        $response = $this->post(route('cabinet.clients.store'), $this->baseData);
        $response->assertSessionHasErrors(['legal_form', 'first_name', 'last_name'])
            ->assertInvalid(['legal_form', 'first_name', 'last_name']);
    }

    #[Test]
    public function it_allows_nullable_fields_to_be_empty()
    {
        $this->baseData['address'] = null;
        $this->baseData['city'] = null;
        $this->baseData['country'] = null;
        $this->baseData['postal_code'] = null;
        $this->baseData['note'] = null;

        $response = $this->post(route('cabinet.clients.store'), $this->baseData);
        $response->assertSessionHas('success')
            ->assertSessionHasNoErrors()
            ->assertValid(['address', 'city', 'country', 'postal_code', 'note']);
    }

    #[Test]
    public function it_fails_when_unique_value_is_not_unique()
    {
        $uniqueInputsToTest = ['id_number', 'vat_number', 'email'];
        $this->baseData['id_number'] = 'unique_id';
        $this->baseData['vat_number'] = 'unique_vat';
        $this->baseData['email'] = 'unique@email';
        $this->post(route('cabinet.clients.store'), $this->baseData);

        $this->baseData['id_number'] = 'unique_id';
        $this->baseData['vat_number'] = 'unique_vat';
        $this->baseData['email'] = 'unique@email';
        $response = $this->post(route('cabinet.clients.store'), $this->baseData);
        $response->assertSessionHasErrors($uniqueInputsToTest)
            ->assertInvalid($uniqueInputsToTest);
        dump(
            session()->get('errors')?->toArray()
        );
    }

    #[Test]
    public function it_fails_when_bank_account_is_not_unique()
    {
        $this->baseData['bank_accounts'] = [
            ['bank_id' => $this->bank->id, 'bank_account' => 'duplicate_account'], // Updated account
            ['bank_id' => $this->bank->id, 'bank_account' => 'duplicate_account'], // Updated account
        ];

        $response = $this->post(route('cabinet.clients.store'), $this->baseData);
        $response->assertSessionHasErrors('bank_accounts.*.bank_account');
    }

    #[Test]
    public function it_validates_all_inputs_successfully()
    {
        $inputsToTest = [
            'email' => [null, '', [1, 2, 3], 'not-email', Str::random(256)],
            'due_days' => [null, '', 'string', -1, 1.1, 366],
            'discount_rate' => [null, '', 'string', -1, 101],
            'entity_type' => [null, '', [1, 2, 3], Str::random(16), 'invalid_type'],
            'legal_form' => [null, '', [1, 2, 3], Str::random(32), 'invalid_legal_form'],
            'name' => [null, '', [1, 2, 3], Str::random(128)],
            // 'first_name' => [null, '', [1, 2, 3], Str::random(128)],
            // 'last_name' => [null, '', [1, 2, 3], Str::random(128)],
            'id_number' => [null, '', [1, 2, 3], Str::random(12)],
            'vat_number' => [[1, 2, 3], Str::random(14)],
            'address' => [[1, 2, 3], Str::random(64)],
            'city' => [[1, 2, 3], Str::random(32)],
            'country' => [[1, 2, 3], Str::random(32)],
            'postal_code' => [[1, 2, 3], Str::random(16)],
            'note' => [[1, 2, 3], Str::random(512)],
            'bank_accounts' => ['string'],
            // 'bank_accounts.*.bank_id' => [null, '', 999999],
            // 'bank_accounts.*.bank_account' => [null, '', [1, 2, 3], Str::random(32), 'duplicate_account'],
            'bank_accounts.0.bank_id' => [null, 999998],
            'bank_accounts.0.bank_account' => [null, [1, 2, 3], Str::random(32)],
            'bank_accounts.1.bank_id' => ['', 999999],
            'bank_accounts.1.bank_account' => ['', [3, 2, 1], Str::random(32)],
        ];

        foreach ($inputsToTest as $input => $invalidValues) {
            foreach ($invalidValues as $invalidValue) {
                $this->checkIfValidationFails($input, $invalidValue);
            }
        }
    }

    protected function checkIfValidationFails(string $input, mixed $invalidValue): void
    {
        $data = $this->getModifiedData($input, $invalidValue);
        $response = $this->post(route('cabinet.clients.store'), $data);
        $response->assertSessionHasErrors($input)
            ->assertInvalid($input);
        $invalidValueAsString =  is_string($invalidValue) ? $invalidValue : json_encode($invalidValue);
        dump("$input: $invalidValueAsString", session()->get('errors')?->toArray());
    }

    protected function getModifiedData(string $input, mixed $value): array
    {
        $data = $this->baseData;
        data_set($data, $input, $value);

        return $data;
    }

    protected function getValidBaseData(): array
    {
        return [
            'entity_type' => 'legal_entity',
            'legal_form' => 'llc',
            'name' => 'Test Company',
            'id_number' => '12345678901',
            'vat_number' => '1234567890123',
            'address' => '123 Street',
            'city' => 'City',
            'country' => 'Country',
            'postal_code' => '12345',
            'note' => 'Test note',
            'email' => 'testcompany@example.com',
            'due_days' => 30,
            'discount_rate' => 5,
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '123456789012'],
                ['bank_id' => $this->bank->id, 'bank_account' => '987654321098'],
            ],
        ];
    }
}
