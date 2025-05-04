<?php

namespace Tests\Unit\Repositories;

use App\Models\User\Agent;
use App\Models\User\User;
use App\Repositories\AgentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AgentRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private AgentRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AgentRepository();

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_gets_all_agents_for_user()
    {
        // Arrange
        $agents = Agent::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        // Create agents for another user (shouldn't be returned)
        $anotherUser = User::factory()->create();
        Agent::factory()->count(2)->create([
            'user_id' => $anotherUser->id,
        ]);

        // Act
        $result = $this->repository->getAllForUser();

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Agent::class, $result->first());
        $this->assertEquals($agents->pluck('id')->sort()->values(), $result->pluck('id')->sort()->values());
    }

    #[Test]
    public function it_finds_agent_by_id()
    {
        // Arrange
        $agents = Agent::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $agent = $agents->first(); // Get the first agent from the collection

        // Act
        $result = $this->repository->find($agent->id);

        // Assert
        $this->assertInstanceOf(Agent::class, $result);
        $this->assertEquals($agent->id, $result->id);
    }

    #[Test]
    public function it_throws_exception_when_finding_nonexistent_agent()
    {
        // Arrange
        $nonExistentId = 999999;

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->find($nonExistentId);
    }

    #[Test]
    public function it_creates_agent()
    {
        // Arrange
        $data = [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
            'role' => 'role',
        ];

        // Act
        $agent = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(Agent::class, $agent);
        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'user_id' => $this->user->id,
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
            'role' => 'role',
        ]);
    }

    #[Test]
    public function it_updates_agent_by_id()
    {
        // Arrange
        $agent = Agent::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
            'role' => 'role',
        ];

        // Act
        $result = $this->repository->updateById($agent->id, $data);

        // Assert
        $this->assertInstanceOf(Agent::class, $result);
        $this->assertEquals($agent->id, $result->id);
        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'user_id' => $this->user->id,
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
            'role' => 'role',
        ]);
    }

    #[Test]
    public function it_throws_exception_when_updating_nonexistent_agent_by_id()
    {
        // Arrange
        $nonExistentId = 999999;
        $data = [
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
        ];

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->updateById($nonExistentId, $data);
    }

    #[Test]
    public function it_updates_agent_model()
    {
        // Arrange
        $agent = Agent::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
            'role' => 'role',
        ];

        // Act
        $result = $this->repository->update($agent, $data);

        // Assert
        $this->assertInstanceOf(Agent::class, $result);
        $this->assertEquals($agent->id, $result->id);
        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'user_id' => $this->user->id,
            'first_name' => 'first name',
            'last_name' => 'last name',
            'position' => 'position',
            'email' => 'email',
            'role' => 'role',
        ]);
    }

    #[Test]
    public function it_deletes_agent_by_id()
    {
        // Arrange
        $agents = Agent::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $agent = $agents->first(); // Get the first agent from the collection

        // Act
        $result = $this->repository->deleteById($agent->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('agents', [
            'id' => $agent->id,
        ]);
    }

    #[Test]
    public function it_throws_exception_when_deleting_nonexistent_agent_by_id()
    {
        // Arrange
        $nonExistentId = 9999;

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->deleteById($nonExistentId);
    }

    #[Test]
    public function it_deletes_agent_model()
    {
        // Arrange
        $agents = Agent::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $agent = $agents->first(); // Get the first agent from the collection

        // Act
        $result = $this->repository->delete($agent);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('agents', [
            'id' => $agent->id,
        ]);
    }

    #[Test]
    public function it_respects_user_isolation_for_agents()
    {
        // Arrange:
        // Create agent for current user
        $userAgent = Agent::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create agent for another user
        $anotherUser = User::factory()->create();
        $anotherUserAgent = Agent::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $result = $this->repository->getAllForUser();

        // Assert
        $this->assertContains($userAgent->id, $result->pluck('id'));
        $this->assertNotContains($anotherUserAgent->id, $result->pluck('id'));
    }
}
