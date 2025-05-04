<?php

namespace Tests\Unit\Services;

use App\Models\User\User;
use App\Models\Entity\Entity;
use App\Models\User\Bank;
use App\Models\Entity\EntityBankAccount;
use App\Services\SellerEntityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SellerEntityServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected SellerEntityService $service;
    private User $user;
    private Bank $bank;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(SellerEntityService::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);

        // Clear the banks table before each test
        Bank::truncate();
        $this->bank = Bank::factory()->create(['name' => 'Test Bank ' . uniqid()]);
    }

    #[Test]
    public function it_can_create_seller_with_related_data()
    {
        // Arrange
        $data = [
            'entityable_type' => 'client',
            'entityable_id' => $this->user->id,
            'entity_type' => 'legal_entity',
            'legal_form' => 'llc',
            'name' => 'Acme Corp',
            'id_number' => '123456789',
            'vat_number' => 'GB123456789',
            'address' => '123 Main St',
            'city' => 'London',
            'country' => 'UK',
            'postal_code' => 'SW1A 1AA',
            'note' => 'This is a test note.',
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '123456789012'],
                ['bank_id' => $this->bank->id, 'bank_account' => '987654321098'],
            ],
        ];

        // Act
        $entity = $this->service->createEntity($data);

        // Assert
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals($this->user->id, $entity->entityable_id);
        $this->assertEquals('user', $entity->entityable_type);
        $this->assertEquals('legal_entity', $entity->entity_type);
        $this->assertEquals('Acme Corp', $entity->name);
        $this->assertEquals('123456789', $entity->id_number);

        $bankAccounts = $entity->bankAccounts;
        $this->assertCount(2, $bankAccounts);
        $this->assertEquals('123456789012', $bankAccounts[0]->bank_account);
        $this->assertEquals('987654321098', $bankAccounts[1]->bank_account);
    }

    #[Test]
    public function it_can_update_seller_with_related_data()
    {
        // Arrange
        $user = User::factory()->has(Entity::factory())->create();
        $data = [
            'entity_type' => 'individual',
            'legal_form' => 'self_employed',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_number' => '987654321',
            'address' => '456 Elm St',
            'city' => 'Manchester',
            'country' => 'UK',
            'postal_code' => 'M1 1AA',
            'note' => 'Updated note.',
            'bank_accounts' => [
                ['bank_id' => $this->bank->id, 'bank_account' => '567890123456'],
            ],
        ];

        // Act
        $entity = $this->service->updateEntity($data, $user->entity);

        // Assert
        $this->assertInstanceOf(Entity::class, $entity);
        $this->assertEquals('individual', $entity->entity_type);
        $this->assertEquals('John', $entity->first_name);
        $this->assertEquals('Doe', $entity->last_name);
        $this->assertEquals('987654321', $entity->id_number);

        $bankAccounts = $entity->bankAccounts;
        $this->assertCount(1, $bankAccounts);
        $this->assertEquals('567890123456', $bankAccounts[0]->bank_account);
    }
}
