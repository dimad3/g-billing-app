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

class DocumentSettingSaveRequestTest extends TestCase
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
        // dd($this->baseData);
        $response = $this->post(route('cabinet.settings.store'), $this->baseData);

        $response->assertRedirect(route('cabinet.settings'))
            ->assertSessionHas('success', 'Settings data saved successfully.')
            ->assertValid();

        $this->assertDatabaseHas('document_settings', [
            'number_prefix' => $this->baseData['number_prefix'],
            'next_number' => $this->baseData['next_number'],
            'default_agent_id' => $this->baseData['default_agent_id'],
            'default_tax_rate' => $this->baseData['default_tax_rate'],
        ]);
    }

    #[Test]
    public function it_validates_all_inputs_successfully()
    {
        $inputsToTest = [
            'number_prefix' => [null, '', [1, 2, 3], Str::random(16)],
            'next_number' => [null, '', 'string', 0.1, -1, 4294967296],
            'default_agent_id' => [null, '', 'string', 0.1, 999999],
            'default_tax_rate' => [null, '', 'string', -0.1, 100.1],
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
        $response = $this->post(route('cabinet.settings.store'), $data);
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
        $userAgents = Agent::factory()->count(5)->for($this->user)->create();
        ($userAgentsIds = $userAgents->pluck('id')->toArray());

        return [
            'number_prefix' => 'valid prefix',
            'next_number' => 33,
            'default_agent_id' => fake()->randomElement($userAgentsIds),
            'default_tax_rate' => 97.5,
        ];
    }
}
