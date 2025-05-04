<?php

namespace Tests\Feature\Cabinet\User;

use App\Http\Controllers\Cabinet\User\ClientController;
use App\Models\User\User;
use App\Models\User\Client;
use App\Models\User\Bank;
use App\Models\Entity\Entity;
use App\Models\Entity\EntityBankAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ClientControllerTest extends TestCase
{
    use DatabaseTransactions;

    private ClientController $controller;
    private User $user;
    private Bank $bank;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->controller = new ClientController();
        $this->controller = app(ClientController::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);

        // Clear the banks table before each test
        Bank::truncate();
        $this->bank = Bank::factory()->create();
    }

    #[Test]
    public function it_displays_client_index_page_with_paginated_clients()
    {
        $clients = Client::factory()->count(15)->for($this->user)
            ->has(Entity::factory(), 'entity')
            ->create();

        $response = $this->get(route('cabinet.clients.index'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.clients.index')
            ->assertViewHas('clients')
            ->assertSee('Clients List');
        // ->assertViewHasPaginationCount(10);
    }

    #[Test]
    public function it_shows_create_client_form()
    {
        $response = $this->get(route('cabinet.clients.create'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.clients.create_or_edit')
            ->assertSee('Add New Client')
            ->assertViewHas(['client', 'entityTypes', 'banks', 'requiredInputs']);
    }

    #[Test]
    public function it_shows_edit_client_form()
    {
        $client = Client::factory()->for($this->user)
            ->has(Entity::factory()
                    ->has(EntityBankAccount::factory()->count(3), 'bankAccounts'), 'entity')
            ->create();

        $response = $this->get(route('cabinet.clients.edit', $client));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.clients.create_or_edit')
            ->assertSee($client->entity->name ?? $client->entity->first_name)
            ->assertViewHas(['client', 'entityTypes', 'banks', 'requiredInputs']);


        // Assert: Check if the entity bank accounts are passed to the view
        $this->assertEquals(
            $client->entity->bankAccounts->toArray(),
            $response->viewData('client')->entity->bankAccounts->toArray()
        );
    }

    #[Test]
    public function it_stores_new_client_successfully()
    {
        $clientData = [
            'entity_type' => 'legal_entity',
            'legal_form' => 'llc',
            'name' => 'Test Company',
            'id_number' => '12345678901',
            'email' => 'testcompany@example.com',
            'due_days' => 30,
            'discount_rate' => 5.0,
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '0987654321'],
                ['bank_id' => $this->bank->id, 'bank_account' => '1234567890'],
            ]
        ];

        $response = $this->post(route('cabinet.clients.store'), $clientData);

        $response->assertRedirect(route('cabinet.clients.index'))
            ->assertSessionHas('success', 'Client created successfully.');

        $this->assertDatabaseHas('clients', [
            'email' => 'testcompany@example.com',
            'due_days' => 30,
            'discount_rate' => 5.0
        ]);

        $client = Client::where('email', 'testcompany@example.com')->first()->loadMissing('entity');

        $this->assertDatabaseHas('entities', [
            'entityable_id' => $client->id,
            'name' => 'Test Company',
            'id_number' => '12345678901'
        ]);

        $bankAccounts = $client->entity->bankAccounts;
        $this->assertCount(2, $bankAccounts);
        $this->assertDatabaseHas('entity_bank_accounts', [
            'bank_account' => '0987654321',
            'bank_account' => '1234567890',
        ]);
    }

    #[Test]
    public function it_updates_existing_client_successfully()
    {
        $client = Client::factory()->for($this->user)
            ->has(Entity::factory(), 'entity')
            ->create();

        $updatedData = [
            'entity_type' => 'individual',
            'legal_form' => 'self_employed',
            'first_name' => 'Updated', // for entity test
            'last_name' => 'Name', // for entity test
            'id_number' => '98765432109',
            'email' => 'updatedcompany@example.com',
            'due_days' => 45,
            'discount_rate' => 7.5,
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '0987654321'],
                ['bank_id' => $this->bank->id, 'bank_account' => '1234567890'],
            ]
        ];
        // dd($client->entity?->getAttributes());

        $response = $this->put(route('cabinet.clients.update', $client), $updatedData);

        $response->assertRedirect(route('cabinet.clients.index'))
            ->assertSessionHas('success', 'Client updated successfully.');
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'email' => 'updatedcompany@example.com',
            'due_days' => 45,
            'discount_rate' => 7.5
        ]);
        $this->assertDatabaseHas('entities', [
            'entityable_id' => $client->id,
            'first_name' => 'Updated',
            'last_name' => 'Name'
        ]);
        // dd($client->entity->getAttributes());
        $bankAccounts = $client->entity->bankAccounts;
        $this->assertCount(2, $bankAccounts);
        $this->assertDatabaseHas('entity_bank_accounts', [
            'bank_account' => '0987654321',
            'bank_account' => '1234567890',
        ]);
    }

    #[Test]
    public function it_deletes_client_successfully()
    {
        $client = Client::factory()->for($this->user)
            ->has(Entity::factory(), 'entity')
            ->create();

        // Refresh the entity from the database to ensure we have all relationship data
        // $client->refresh();

        $clientClone = $client->entity->replicate(); // after delete, client may by deleted too
        $entityClone = $client->entity->replicate(); // after delete, entity will be deleted too

        $response = $this->delete(route('cabinet.clients.destroy', [$client]));

        $response->assertRedirect(route('cabinet.clients.index'))
            ->assertSessionHas('success', 'Client deleted successfully.');

        $this->assertDatabaseMissing('clients', ['id' => $clientClone->id]);
        $this->assertDatabaseMissing('entities', ['entityable_id' => $entityClone->id]);
    }

    #[Test]
    public function it_prevents_client_creation_with_invalid_data()
    {
        $invalidData = [
            'entity_type' => '', // Required field
            'legal_form' => '', // Required field
            'id_number' => '', // Required field
            'email' => 'invalid-email',
            'due_days' => '', // Required field
            'discount_rate' => '', // Required field
        ];

        $response = $this->post(route('cabinet.clients.store'), $invalidData);

        $response->assertSessionHasErrors([
            'entity_type',
            'legal_form',
            'id_number',
            'email',
            'due_days',
            'discount_rate',
        ]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_other_users_clients()
    {
        $otherUser = User::factory()->create();
        $otherUserClient = Client::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get(route('cabinet.clients.edit', $otherUserClient));

        $response->assertNotFound();
    }
}
