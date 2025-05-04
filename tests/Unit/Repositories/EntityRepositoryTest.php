<?php

namespace Tests\Unit\Repositories;

use App\Models\Entity\Entity;
use App\Models\User\Client;
use App\Models\User\User;
use App\Repositories\EntityRepository;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EntityRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected EntityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EntityRepository();
    }

    #[Test]
    public function it_can_find_entity_by_id()
    {
        // Create an entity
        $entity = Entity::factory()->create();

        // Find the entity by ID
        $foundEntity = $this->repository->find($entity->id);

        // Assert entity is found
        $this->assertInstanceOf(Entity::class, $foundEntity);
        $this->assertEquals($entity->id, $foundEntity->id);
    }

    #[Test]
    public function it_throws_exception_when_finding_non_existent_entity()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Model not found.");

        // Try to find a non-existent entity
        $this->repository->find(999);
    }

    #[Test]
    public function it_can_create_entity_for_client()
    {
        // Create a client
        $client = Client::factory()->create();

        // Data for a legal entity
        $entity_type = fake()->randomElement(array_keys(config('static_data.entity_types')));
        $legal_form = fake()->randomElement(array_keys(config("static_data.legal_forms.$entity_type")));
        $name = $entity_type === 'legal_entity' ? 'Test Company' : null;
        $first_name = $entity_type === 'individual' ? 'first name' : null;
        $last_name = $entity_type === 'individual' ? 'last name' : null;
        $data = [
            'entity_type' => $entity_type,
            'legal_form' => $legal_form,
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'id_number' => '12345678',
            'vat_number' => 'VAT12345',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'note' => 'Test note'
        ];

        // Create an entity for the client
        $entity = $this->repository->create($client, $data);

        // Refresh the entity from the database to ensure we have all relationship data
        $entity->refresh();

        // Assert entity is created with correct data
        $this->assertInstanceOf(Entity::class, $entity);

        // Check the client relationship instead of directly accessing client_id
        $this->assertTrue($entity->entityable->exists());
        $this->assertEquals($client->id, $entity->entityable->id);

        $this->assertEquals($entity_type, $entity->entity_type);
        $this->assertEquals($legal_form, $entity->legal_form);
        $this->assertEquals($name, $entity->name);
        $this->assertEquals($first_name, $entity->first_name);
        $this->assertEquals($last_name, $entity->last_name);
        $this->assertEquals('12345678', $entity->id_number);
        $this->assertEquals('VAT12345', $entity->vat_number);
        $this->assertEquals('123 Test Street', $entity->address);
        $this->assertEquals('Test City', $entity->city);
        $this->assertEquals('12345', $entity->postal_code);
        $this->assertEquals('Test Country', $entity->country);
        $this->assertEquals('Test note', $entity->note);
    }

    #[Test]
    public function it_can_create_entity_for_user()
    {
        // Create a user
        $user = User::factory()->create();

        // Data for a legal entity
        $entity_type = fake()->randomElement(array_keys(config('static_data.entity_types')));
        $legal_form = fake()->randomElement(array_keys(config("static_data.legal_forms.$entity_type")));
        $name = $entity_type === 'legal_entity' ? 'Test Company' : null;
        $first_name = $entity_type === 'individual' ? 'first name' : null;
        $last_name = $entity_type === 'individual' ? 'last name' : null;
        $data = [
            'entity_type' => $entity_type,
            'legal_form' => $legal_form,
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'id_number' => '12345678',
            'vat_number' => 'VAT12345',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'note' => 'Test note'
        ];

        // Create an entity for the client
        $entity = $this->repository->create($user, $data);

        // Refresh the entity from the database to ensure we have all relationship data
        $entity->refresh();

        // Assert entity is created with correct data
        $this->assertInstanceOf(Entity::class, $entity);

        // Check the client relationship instead of directly accessing client_id
        $this->assertTrue($entity->entityable->exists());
        $this->assertEquals($user->id, $entity->entityable->id);

        $this->assertEquals($entity_type, $entity->entity_type);
        $this->assertEquals($legal_form, $entity->legal_form);
        $this->assertEquals($name, $entity->name);
        $this->assertEquals($first_name, $entity->first_name);
        $this->assertEquals($last_name, $entity->last_name);
        $this->assertEquals('12345678', $entity->id_number);
        $this->assertEquals('VAT12345', $entity->vat_number);
        $this->assertEquals('123 Test Street', $entity->address);
        $this->assertEquals('Test City', $entity->city);
        $this->assertEquals('12345', $entity->postal_code);
        $this->assertEquals('Test Country', $entity->country);
        $this->assertEquals('Test note', $entity->note);
    }

    #[Test]
    public function it_can_create_legal_entity_for_client()
    {
        // Create a client
        $client = Client::factory()->create();

        // Data for a legal entity
        $data = [
            'entity_type' => 'legal_entity',
            'legal_form' => 'LLC',
            'name' => 'Test Company',
            'id_number' => '12345678',
            'vat_number' => 'VAT12345',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'postal_code' => '12345',
            'country' => 'Test Country',
            'note' => 'Test note'
        ];

        // Create an entity for the client
        $entity = $this->repository->createForClient($client, $data);

        // Refresh the entity from the database to ensure we have all relationship data
        $entity->refresh();

        // Assert entity is created with correct data
        $this->assertInstanceOf(Entity::class, $entity);

        // Check the client relationship instead of directly accessing client_id
        $this->assertTrue($entity->entityable->exists());
        $this->assertEquals($client->id, $entity->entityable->id);

        $this->assertEquals('legal_entity', $entity->entity_type);
        $this->assertEquals('LLC', $entity->legal_form);
        $this->assertEquals('Test Company', $entity->name);
        $this->assertNull($entity->first_name);
        $this->assertNull($entity->last_name);
        $this->assertEquals('12345678', $entity->id_number);
        $this->assertEquals('VAT12345', $entity->vat_number);
        $this->assertEquals('123 Test Street', $entity->address);
        $this->assertEquals('Test City', $entity->city);
        $this->assertEquals('12345', $entity->postal_code);
        $this->assertEquals('Test Country', $entity->country);
        $this->assertEquals('Test note', $entity->note);
    }

    #[Test]
    public function it_can_create_natural_person_entity_for_client()
    {
        // Create a client
        $client = Client::factory()->create();

        // Data for a natural person entity
        $data = [
            'entity_type' => 'natural_person',
            'legal_form' => 'Individual',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_number' => '12345678',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'postal_code' => '12345',
            'country' => 'Test Country'
        ];

        // Create an entity for the client
        $entity = $this->repository->createForClient($client, $data);

        // Refresh the entity from the database
        $entity->refresh();

        // Assert entity is created with correct data
        $this->assertInstanceOf(Entity::class, $entity);

        // Check the client relationship
        $this->assertTrue($entity->entityable->exists());
        $this->assertEquals($client->id, $entity->entityable->id);

        $this->assertEquals('natural_person', $entity->entity_type);
        $this->assertEquals('Individual', $entity->legal_form);
        $this->assertNull($entity->name);
        $this->assertEquals('John', $entity->first_name);
        $this->assertEquals('Doe', $entity->last_name);
        $this->assertEquals('12345678', $entity->id_number);
        $this->assertNull($entity->vat_number);
    }

    #[Test]
    public function it_can_update_entity_by_id()
    {
        // Create an entity
        $entity = Entity::factory()->create([
            'entity_type' => 'legal_entity',
            'name' => 'Original Company'
        ]);

        // Update data
        $updateData = [
            'entity_type' => 'legal_entity',
            'legal_form' => 'Corp',
            'name' => 'Updated Company',
            'id_number' => '87654321',
            'vat_number' => 'VAT54321',
            'address' => 'Updated Address',
            'city' => 'Updated City',
            'postal_code' => '54321',
            'country' => 'Updated Country',
            'note' => 'Updated note'
        ];

        // Update the entity
        $updatedEntity = $this->repository->updateById($entity->id, $updateData);

        // Assert entity is updated with correct data
        $this->assertEquals($entity->id, $updatedEntity->id);
        $this->assertEquals('legal_entity', $updatedEntity->entity_type);
        $this->assertEquals('Corp', $updatedEntity->legal_form);
        $this->assertEquals('Updated Company', $updatedEntity->name);
        $this->assertEquals('87654321', $updatedEntity->id_number);
        $this->assertEquals('VAT54321', $updatedEntity->vat_number);
        $this->assertEquals('Updated Address', $updatedEntity->address);
        $this->assertEquals('Updated City', $updatedEntity->city);
        $this->assertEquals('54321', $updatedEntity->postal_code);
        $this->assertEquals('Updated Country', $updatedEntity->country);
        $this->assertEquals('Updated note', $updatedEntity->note);
    }

    #[Test]
    public function it_throws_exception_when_updating_non_existent_entity_by_id()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Model not found.");

        // Try to update a non-existent entity
        $this->repository->updateById(999, [
            'entity_type' => 'legal_entity',
            'legal_form' => 'Corp',
            'name' => 'Updated Company',
            'id_number' => '87654321'
        ]);
    }

    #[Test]
    public function it_can_update_entity_model()
    {
        // Create an entity
        $entity = Entity::factory()->create([
            'entity_type' => 'legal_entity',
            'name' => 'Original Company'
        ]);

        // Update data
        $updateData = [
            'entity_type' => 'legal_entity',
            'legal_form' => 'Corp',
            'name' => 'Updated Company',
            'id_number' => '87654321'
        ];

        // Update the entity
        $updatedEntity = $this->repository->update($entity, $updateData);

        // Assert entity is updated with correct data
        $this->assertEquals($entity->id, $updatedEntity->id);
        $this->assertEquals('legal_entity', $updatedEntity->entity_type);
        $this->assertEquals('Corp', $updatedEntity->legal_form);
        $this->assertEquals('Updated Company', $updatedEntity->name);
        $this->assertEquals('87654321', $updatedEntity->id_number);
    }

    #[Test]
    public function it_can_change_entity_type_from_legal_to_natural()
    {
        // Create a legal entity
        $entity = Entity::factory()->create([
            'entity_type' => 'legal_entity',
            'name' => 'Original Company',
            'first_name' => null,
            'last_name' => null
        ]);

        // Update data to change to natural person
        $updateData = [
            'entity_type' => 'natural_person',
            'legal_form' => 'Individual',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_number' => '87654321'
        ];

        // Update the entity
        $updatedEntity = $this->repository->updateById($entity->id, $updateData);

        // Assert entity type is changed
        $this->assertEquals('natural_person', $updatedEntity->entity_type);
        $this->assertNull($updatedEntity->name);
        $this->assertEquals('John', $updatedEntity->first_name);
        $this->assertEquals('Doe', $updatedEntity->last_name);
    }

    #[Test]
    public function it_can_change_entity_type_from_natural_to_legal()
    {
        // Create a natural person entity
        $entity = Entity::factory()->create([
            'entity_type' => 'natural_person',
            'name' => null,
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        // Update data to change to legal entity
        $updateData = [
            'entity_type' => 'legal_entity',
            'legal_form' => 'LLC',
            'name' => 'New Company',
            'id_number' => '87654321'
        ];

        // Update the entity
        $updatedEntity = $this->repository->updateById($entity->id, $updateData);

        // Assert entity type is changed
        $this->assertEquals('legal_entity', $updatedEntity->entity_type);
        $this->assertEquals('New Company', $updatedEntity->name);
        $this->assertNull($updatedEntity->first_name);
        $this->assertNull($updatedEntity->last_name);
    }

    #[Test]
    public function it_can_delete_entity_by_id()
    {
        // Create an entity
        $entity = Entity::factory()->create();

        // Delete the entity
        $result = $this->repository->deleteById($entity->id);

        // Assert entity is deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('entities', ['id' => $entity->id]);
    }

    #[Test]
    public function it_throws_exception_when_deleting_non_existent_entity_by_id()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Model not found.");

        // Try to delete a non-existent entity
        $this->repository->deleteById(999);
    }

    #[Test]
    public function it_can_delete_entity_model()
    {
        // Create an entity
        $entity = Entity::factory()->create();

        // Delete the entity
        $result = $this->repository->delete($entity);

        // Assert entity is deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('entities', ['id' => $entity->id]);
    }
}
