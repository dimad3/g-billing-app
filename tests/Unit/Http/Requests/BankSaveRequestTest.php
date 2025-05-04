<?php

namespace Tests\Unit\Http\Requests;

use App\Models\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankSaveRequestTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected array $baseData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Auth::login($this->user);

        $this->baseData = $this->getValidBaseData();
    }

    #[Test]
    public function it_validates_successfully_with_correct_data()
    {
        $response = $this->post(route('cabinet.banks.store'), $this->baseData);

        $response->assertRedirect(route('cabinet.banks.index'))
            ->assertSessionHas('success', 'Bank created successfully.')
            ->assertValid();

        $this->assertDatabaseHas('banks', [
            'name' => $this->baseData['name'],
            'bank_code' => $this->baseData['bank_code'],
        ]);
    }

    #[Test]
    public function it_fails_when_unique_value_is_not_unique()
    {
        $uniqueInputsToTest = ['bank_code'];
        $this->baseData['bank_code'] = 'uniquebc';
        $this->post(route('cabinet.banks.store'), $this->baseData);

        $this->baseData['bank_code'] = 'uniquebc';
        $response = $this->post(route('cabinet.banks.store'), $this->baseData);
        $response->assertSessionHasErrors($uniqueInputsToTest)
            ->assertInvalid($uniqueInputsToTest);
    }

    #[Test]
    public function it_validates_all_inputs_successfully()
    {
        $inputsToTest = [
            'name' => [null, '', [1, 2, 3], Str::random(33)],
            'bank_code' => [null, '', [1, 2, 3], Str::random(9)],
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
        $response = $this->post(route('cabinet.banks.store'), $data);
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
            'name' => 'valid name',
            'bank_code' => 'bankcode',
        ];
    }
}
