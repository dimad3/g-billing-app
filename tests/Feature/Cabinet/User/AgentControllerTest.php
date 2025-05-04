<?php

namespace Tests\Feature\Cabinet\User;

use App\Http\Controllers\Cabinet\User\AgentController;
use App\Models\User\User;
use App\Models\User\Agent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AgentControllerTest extends TestCase
{
    use DatabaseTransactions;

    private AgentController $controller;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = app(AgentController::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_displays_agent_index_page_with_paginated_agents()
    {
        $agents = Agent::factory()->count(15)->for($this->user)->create();

        $response = $this->get(route('cabinet.agents.index'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.agents.index')
            ->assertViewHas('agents')
            ->assertSee('Employees List');
    }

    #[Test]
    public function it_shows_create_agent_form()
    {
        $response = $this->get(route('cabinet.agents.create'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.agents.create_or_edit')
            ->assertSee('Add New Employee')
            ->assertViewHas(['agent', 'roles', 'requiredInputs']);
    }

    #[Test]
    public function it_shows_edit_agent_form()
    {
        $agent = Agent::factory()->for($this->user)->create();

        $response = $this->get(route('cabinet.agents.edit', $agent));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.agents.create_or_edit')
            ->assertSee($agent->last_name)
            ->assertViewHas(['agent', 'roles', 'requiredInputs']);
    }

    #[Test]
    public function it_stores_new_agent_successfully()
    {
        $data = [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'test_agent@example.com',
            'role' => 'admin',
        ];

        $response = $this->post(route('cabinet.agents.store'), $data);

        $response->assertRedirect(route('cabinet.agents.index'))
            ->assertSessionHas('success', 'Employee created successfully.');

        $this->assertDatabaseHas('agents', [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'test_agent@example.com',
            'role' => 'admin',
        ]);
    }

    #[Test]
    public function it_updates_existing_agent_successfully()
    {
        $agent = Agent::factory()->for($this->user)->create();

        $updatedData = [
            'first_name' => 'Updated',
            'last_name' => 'Last Name',
            'position' => 'Updated position',
            'email' => 'updated_agent@example.com',
            'role' => 'user',
        ];

        $response = $this->put(route('cabinet.agents.update', $agent), $updatedData);

        $response->assertRedirect(route('cabinet.agents.index'))
            ->assertSessionHas('success', 'Employee updated successfully.');
        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'first_name' => 'Updated',
            'last_name' => 'Last Name',
            'position' => 'Updated position',
            'email' => 'updated_agent@example.com',
            'role' => 'user',
        ]);
    }

    #[Test]
    public function it_deletes_agent_successfully()
    {
        $agent = Agent::factory()->for($this->user)->create();

        $agentClone = $agent->replicate(); // after delete, agent may by deleted too

        $response = $this->delete(route('cabinet.agents.destroy', [$agent]));

        $response->assertRedirect(route('cabinet.agents.index'))
            ->assertSessionHas('success', 'Employee deleted successfully.');

        $this->assertDatabaseMissing('agents', ['id' => $agentClone->id]);
    }

    #[Test]
    public function it_prevents_agent_creation_with_invalid_data()
    {
        $invalidData = [
            'first_name' => '', // Required field
            'last_name' => '', // Required field
            'position' => '', // Required field
            'email' => 'invalid-email',
            'role' => 'moderator', // not in the list
        ];

        $response = $this->post(route('cabinet.agents.store'), $invalidData);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'position',
            'email',
            'role',
        ]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_other_users_agents()
    {
        $otherUser = User::factory()->create();
        $otherUserAgent = Agent::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get(route('cabinet.agents.edit', $otherUserAgent));
        $response->assertNotFound();

        $response = $this->put(route('cabinet.agents.update', $otherUserAgent));
        $response->assertNotFound();

        $response = $this->delete(route('cabinet.agents.destroy', $otherUserAgent));
        $response->assertNotFound();
    }
}
