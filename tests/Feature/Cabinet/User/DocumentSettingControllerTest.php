<?php

namespace Tests\Feature\Cabinet\User;

use App\Http\Controllers\Cabinet\User\DocumentSettingController;
use App\Models\User\Agent;
use App\Models\User\DocumentSetting;
use App\Models\User\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DocumentSettingControllerTest extends TestCase
{
    use DatabaseTransactions;

    private DocumentSettingController $controller;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = app(DocumentSettingController::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_shows_edit_settings_form()
    {
        $settings = DocumentSetting::factory()->create(
            ['user_id' => $this->user->id]
        );

        $response = $this->get(route('cabinet.settings', $settings));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.settings.edit')
            ->assertSee('Settings')
            ->assertSee($settings->number_prefix)
            ->assertViewHas(['settings', 'agents', 'requiredInputs']);
    }

    #[Test]
    public function it_stores_new_settings_successfully()
    {
        $userAgents = Agent::factory()->count(5)->for($this->user)->create();
        ($userAgentsIds = $userAgents->pluck('id')->toArray());
        $defaultAgentId = fake()->randomElement($userAgentsIds);

        $data = [
            'number_prefix' => 'valid prefix',
            'next_number' => 33,
            'default_agent_id' => $defaultAgentId,
            'default_tax_rate' => 98,
        ];

        ($response = $this->post(route('cabinet.settings.store'), $data));

        $response->assertRedirect(route('cabinet.settings'))
            ->assertSessionHas('success', 'Settings data saved successfully.');

        $this->assertDatabaseHas('document_settings', [
            'number_prefix' => 'valid prefix',
            'next_number' => 33,
            'default_agent_id' => $defaultAgentId,
            'default_tax_rate' => 98,
        ]);
    }

    #[Test]
    public function it_updates_existing_settings_successfully()
    {
        $userAgents = Agent::factory()->count(100)->for($this->user)->create();
        ($userAgentsIds = $userAgents->pluck('id')->toArray());
        $agentId = fake()->randomElement($userAgentsIds);
        $updatedAgentId = fake()->randomElement($userAgentsIds);

        $settings = DocumentSetting::factory()->create(
            [
                'user_id' => $this->user->id,
                'default_agent_id' => $agentId
            ]
        );

        $data = [
            'number_prefix' => 'updated prefix',
            'next_number' => 33,
            'default_agent_id' => $updatedAgentId,
            'default_tax_rate' => 99,
        ];

        $response = $this->put(route('cabinet.settings.update', $settings), $data);

        $response->assertRedirect(route('cabinet.settings'))
            ->assertSessionHas('success', 'Settings updated successfully.');

        $this->assertDatabaseHas('document_settings', [
            'user_id' => $this->user->id,
            'number_prefix' => 'updated prefix',
            'next_number' => 33,
            'default_agent_id' => $updatedAgentId,
            'default_tax_rate' => 99,
        ]);
    }

    #[Test]
    public function it_prevents_settings_creation_with_invalid_data()
    {
        $invalidData = [
            'number_prefix' => '', // Required field
            'next_number' => '', // Required field
            'default_agent_id' => '', // Required field
        ];

        $response = $this->post(route('cabinet.settings.store'), $invalidData);

        $response->assertSessionHasErrors([
            'number_prefix',
            'next_number',
            'default_agent_id',
        ]);
    }

    #[Test]
    public function it_prevents_settings_updating_with_invalid_data()
    {
        $settings = DocumentSetting::factory()->create(
            ['user_id' => $this->user->id]
        );

        $invalidData = [
            'number_prefix' => '', // Required field
            'next_number' => '', // Required field
            'default_agent_id' => '', // Required field
        ];

        $response = $this->put(route('cabinet.settings.update', [$settings]), $invalidData);

        $response->assertSessionHasErrors([
            'number_prefix',
            'next_number',
            'default_agent_id',
        ]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_other_users_settings()
    {
        $otherUser = User::factory()->create();
        $otherUserSettings = DocumentSetting::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $otherUserAgents = Agent::factory()->count(5)->for($otherUser)->create();
        ($otherUserAgentsIds = $otherUserAgents->pluck('id')->toArray());
        $otherUserAgentId = fake()->randomElement($otherUserAgentsIds);

        $data = [
            'number_prefix' => 'valid prefix',
            'next_number' => 33,
            'default_agent_id' => $otherUserAgentId,
        ];

        $response = $this->put(route('cabinet.settings.update', [$otherUserSettings]), $data);

        $response->assertNotFound();
    }
}
