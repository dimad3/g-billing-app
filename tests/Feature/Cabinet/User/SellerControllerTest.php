<?php

namespace Tests\Feature\Cabinet\User;

use App\Http\Controllers\Cabinet\User\SellerController;
use App\Models\User\User;
use App\Models\User\Bank;
use App\Models\Entity\Entity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SellerControllerTest extends TestCase
{
    use DatabaseTransactions;

    private SellerController $controller;
    private User $user;
    private Bank $bank;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->controller = new ClientController();
        $this->controller = app(SellerController::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);

        // Clear the banks table before each test
        Bank::truncate();
        $this->bank = Bank::factory()->create();
    }

    #[Test]
    public function it_shows_edit_seller_form()
    {
        $entity = Entity::factory()->create(
            ['entityable_id' => $this->user->id]
        );
        // dump($entity->getAttributes());
        // dump($entity->name, $entity->first_name);
        $response = $this->get(route('cabinet.seller', $entity));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.seller.edit')
            // ->assertSee($entity->name ?? $entity->first_name) // todo
            ->assertViewHas(['entity', 'entityTypes', 'banks', 'requiredInputs']);
    }

    #[Test]
    public function it_stores_new_seller_successfully()
    {
        $data = [
            'entity_type' => 'legal_entity',
            'legal_form' => 'llc',
            'name' => 'Test Company',
            'id_number' => '12345678901',
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '0987654321'],
                ['bank_id' => $this->bank->id, 'bank_account' => '1234567890'],
            ]
        ];

        ($response = $this->post(route('cabinet.seller.store'), $data));

        $response->assertRedirect(route('cabinet.seller'))
            ->assertSessionHas('success', 'Seller data saved successfully.');

        ($seller = Entity::where('id_number', '12345678901')->first());

        $this->assertDatabaseHas('entities', [
            'entityable_id' => $this->user->id,
            'entity_type' => 'legal_entity',
            'legal_form' => 'llc',
            'name' => 'Test Company',
            'id_number' => '12345678901'
        ]);

        $bankAccounts = $seller->bankAccounts;
        $this->assertCount(2, $bankAccounts);
        $this->assertDatabaseHas('entity_bank_accounts', [
            'bank_account' => '0987654321',
            'bank_account' => '1234567890',
        ]);
    }

    #[Test]
    public function it_updates_existing_seller_successfully()
    {
        $entity = Entity::factory()->create(
            ['entityable_id' => $this->user->id]
        );

        $data = [
            'entity_type' => 'individual',
            'legal_form' => 'self_employed',
            'first_name' => 'Updated first name', // for entity test
            'last_name' => 'Updated last name', // for entity test
            'id_number' => '98765432109',
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '0987654321'],
                ['bank_id' => $this->bank->id, 'bank_account' => '1234567890'],
            ]
        ];
        // dd($client->entity?->getAttributes());

        $response = $this->put(route('cabinet.seller.update', $entity), $data);

        $response->assertRedirect(route('cabinet.seller'))
            ->assertSessionHas('success', 'Seller updated successfully.');

        $this->assertDatabaseHas('entities', [
            'entityable_id' => $this->user->id,
            'first_name' => 'Updated first name',
            'last_name' => 'Updated last name'
        ]);
        // dd($client->entity->getAttributes());
        $bankAccounts = $entity->bankAccounts;
        $this->assertCount(2, $bankAccounts);
        $this->assertDatabaseHas('entity_bank_accounts', [
            'bank_account' => '0987654321',
            'bank_account' => '1234567890',
        ]);
    }

    #[Test]
    public function it_prevents_seller_creation_with_invalid_data()
    {
        $invalidData = [
            'entity_type' => '', // Required field
            'legal_form' => '', // Required field
        ];

        $response = $this->post(route('cabinet.seller.store'), $invalidData);

        $response->assertSessionHasErrors([
            'entity_type',
            'legal_form',
        ]);
    }

    #[Test]
    public function it_prevents_seller_updating_with_invalid_data()
    {
        $entity = Entity::factory()->create(
            ['entityable_id' => $this->user->id]
        );

        $invalidData = [
            'entity_type' => '', // Required field
            'legal_form' => '', // Required field
        ];

        $response = $this->put(route('cabinet.seller.update', [$entity]), $invalidData);

        $response->assertSessionHasErrors([
            'entity_type',
            'legal_form',
        ]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_other_users_sellers()
    {
        $otherUser = User::factory()->create();
        $otherUserSeller = Entity::factory()->create([
            'entityable_id' => $otherUser->id
        ]);

        $data = [
            'entity_type' => 'individual',
            'legal_form' => 'self_employed',
            'first_name' => 'Updated first name', // for entity test
            'last_name' => 'Updated last name', // for entity test
            'id_number' => '98765432109',
        ];

        $response = $this->put(route('cabinet.seller.update', [$otherUserSeller]), $data);

        $response->assertNotFound();
    }
}
