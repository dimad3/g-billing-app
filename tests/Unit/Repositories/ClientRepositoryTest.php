<?php

namespace Tests\Unit\Repositories;

use App\Models\User\Client;
use App\Models\User\User;
use App\Repositories\ClientRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private ClientRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ClientRepository();

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_gets_all_clients_for_user()
    {
        // Arrange
        $clients = Client::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        // Create clients for another user (shouldn't be returned)
        $anotherUser = User::factory()->create();
        Client::factory()->count(2)->create([
            'user_id' => $anotherUser->id,
        ]);

        // Act
        $result = $this->repository->getAllForUser();

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Client::class, $result->first());
        $this->assertEquals($clients->pluck('id')->sort()->values(), $result->pluck('id')->sort()->values());
    }

    #[Test]
    public function it_finds_client_by_id()
    {
        // Arrange
        $clients = Client::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $client = $clients->first(); // Get the first client from the collection

        // Act
        $result = $this->repository->find($client->id);

        // Assert
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($client->id, $result->id);
    }

    #[Test]
    public function it_throws_exception_when_finding_nonexistent_client()
    {
        // Arrange
        $nonExistentId = 999999;

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Model not found.');
        $this->repository->find($nonExistentId);
    }

    #[Test]
    public function it_creates_client()
    {
        // Arrange
        $data = [
            'email' => 'test@example.com',
            'due_days' => 30,
            'discount_rate' => 5.75,
        ];

        // Act
        $client = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(Client::class, $client);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'user_id' => $this->user->id,
            'email' => 'test@example.com',
            'due_days' => 30,
            'discount_rate' => 5.75,
        ]);
    }

    #[Test]
    public function it_updates_client_by_id()
    {
        // Arrange
        $client = Client::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'original@example.com',
            'due_days' => 14,
            'discount_rate' => 0,
        ]);

        $data = [
            'email' => 'updated@example.com',
            'due_days' => 60,
            'discount_rate' => 10.50,
        ];

        // Act
        $result = $this->repository->updateById($client->id, $data);

        // Assert
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($client->id, $result->id);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'email' => 'updated@example.com',
            'due_days' => 60,
            'discount_rate' => 10.50,
        ]);
    }

    #[Test]
    public function it_throws_exception_when_updating_nonexistent_client_by_id()
    {
        // Arrange
        $nonExistentId = 999999;
        $data = [
            'email' => 'updated@example.com',
            'due_days' => 60,
            'discount_rate' => 10.50,
        ];

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Model not found.');
        $this->repository->updateById($nonExistentId, $data);
    }

    #[Test]
    public function it_updates_client_model()
    {
        // Arrange
        $client = Client::factory()->create([
            'user_id' => $this->user->id,
            'email' => 'original@example.com',
            'due_days' => 14,
            'discount_rate' => 0,
        ]);

        $data = [
            'email' => 'updated@example.com',
            'due_days' => 60,
            'discount_rate' => 10.50,
        ];

        // Act
        $result = $this->repository->update($client, $data);

        // Assert
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($client->id, $result->id);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'email' => 'updated@example.com',
            'due_days' => 60,
            'discount_rate' => 10.50,
        ]);
    }

    #[Test]
    public function it_deletes_client_by_id()
    {
        // Arrange
        $clients = Client::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $client = $clients->first(); // Get the first client from the collection

        // Act
        $result = $this->repository->deleteById($client->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    #[Test]
    public function it_throws_exception_when_deleting_nonexistent_client_by_id()
    {
        // Arrange
        $nonExistentId = 9999;

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Model not found.');
        $this->repository->deleteById($nonExistentId);
    }

    #[Test]
    public function it_deletes_client_model()
    {
        // Arrange
        $clients = Client::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $client = $clients->first(); // Get the first client from the collection

        // Act
        $result = $this->repository->delete($client);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id,
        ]);
    }

    #[Test]
    public function it_respects_user_isolation_for_clients()
    {
        // Arrange:
        // Create client for current user
        $userClient = Client::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create client for another user
        $anotherUser = User::factory()->create();
        $anotherUserClient = Client::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        // Mock the Client::forUser scope to verify it's called
        // Note: In a real test, you might need to adjust this based on how your scope is implemented
        $result = $this->repository->getAllForUser();

        // Assert
        $this->assertContains($userClient->id, $result->pluck('id'));
        $this->assertNotContains($anotherUserClient->id, $result->pluck('id'));
    }
}
