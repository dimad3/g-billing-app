<?php

namespace Tests\Unit\Http\Requests;

use App\Models\User\Agent;
use App\Models\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgentSaveRequestTest extends TestCase
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
        $response = $this->post(route('cabinet.agents.store'), $this->baseData);

        $response->assertRedirect(route('cabinet.agents.index'))
            ->assertSessionHas('success', 'Employee created successfully.')
            ->assertValid();

        $this->assertDatabaseHas('agents', [
            'email' => $this->baseData['email'],
        ]);
    }

    #[Test]
    public function it_fails_when_unique_value_is_not_unique()
    {
        $uniqueInputsToTest = ['email'];
        $this->baseData['email'] = 'unique@email';
        $this->post(route('cabinet.agents.store'), $this->baseData);

        $this->baseData['email'] = 'unique@email';
        $response = $this->post(route('cabinet.agents.store'), $this->baseData);
        $response->assertSessionHasErrors($uniqueInputsToTest)
            ->assertInvalid($uniqueInputsToTest);
    }

    #[Test]
    public function it_validates_all_inputs_successfully()
    {
        $inputsToTest = [
            'first_name' => [null, '', [1, 2, 3], Str::random(128)],
            'last_name' => [null, '', [1, 2, 3], Str::random(128)],
            'position' => [null, '', [1, 2, 3], Str::random(128)],
            'email' => [null, '', [1, 2, 3], 'not-email', Str::random(256)],
            'role' => [null, '', [1, 2, 3], Str::random(16), 'invalid_role'],
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
        $response = $this->post(route('cabinet.agents.store'), $data);
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
            'first_name' => 'valid first name',
            'last_name' => 'valid last name',
            'position' => 'valid position',
            'email' => 'validemail@example.com',
            'role' => fake()->randomElement(array_keys(config('static_data.roles'))),
        ];
    }
}
