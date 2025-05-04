<?php

namespace Tests\Unit\Repositories;

use App\Models\User\Bank;
use App\Models\User\User;
use App\Repositories\BankRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BankRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    private BankRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BankRepository();

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_gets_all_banks_for_user()
    {
        // Arrange
        $banks = Bank::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        // Create banks for another user (shouldn't be returned)
        $anotherUser = User::factory()->create();
        Bank::factory()->count(2)->create([
            'user_id' => $anotherUser->id,
        ]);

        // Act
        $result = $this->repository->getAllForUser();

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Bank::class, $result->first());
        $this->assertEquals($banks->pluck('id')->sort()->values(), $result->pluck('id')->sort()->values());
    }

    #[Test]
    public function it_finds_bank_by_id()
    {
        // Arrange
        $banks = Bank::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $bank = $banks->first(); // Get the first bank from the collection

        // Act
        $result = $this->repository->find($bank->id);

        // Assert
        $this->assertInstanceOf(Bank::class, $result);
        $this->assertEquals($bank->id, $result->id);
    }

    #[Test]
    public function it_throws_exception_when_finding_nonexistent_bank()
    {
        // Arrange
        $nonExistentId = 999999;

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->find($nonExistentId);
    }

    #[Test]
    public function it_creates_bank()
    {
        // Arrange
        $data = [
            'name' => 'bank name',
            'bank_code' => 'b_code',
        ];

        // Act
        $bank = $this->repository->create($data);

        // Assert
        $this->assertInstanceOf(Bank::class, $bank);
        $this->assertDatabaseHas('banks', [
            'id' => $bank->id,
            'user_id' => $this->user->id,
            'name' => 'bank name',
            'bank_code' => 'b_code',
        ]);
    }

    #[Test]
    public function it_updates_bank_by_id()
    {
        // Arrange
        $bank = Bank::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ];

        // Act
        $result = $this->repository->updateById($bank->id, $data);

        // Assert
        $this->assertInstanceOf(Bank::class, $result);
        $this->assertEquals($bank->id, $result->id);
        $this->assertDatabaseHas('banks', [
            'id' => $bank->id,
            'user_id' => $this->user->id,
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ]);
    }

    #[Test]
    public function it_throws_exception_when_updating_nonexistent_bank_by_id()
    {
        // Arrange
        $nonExistentId = 999999;
        $data = [
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ];

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->updateById($nonExistentId, $data);
    }

    #[Test]
    public function it_updates_bank_model()
    {
        // Arrange
        $bank = Bank::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ];

        // Act
        $result = $this->repository->update($bank, $data);

        // Assert
        $this->assertInstanceOf(Bank::class, $result);
        $this->assertEquals($bank->id, $result->id);
        $this->assertDatabaseHas('banks', [
            'id' => $bank->id,
            'user_id' => $this->user->id,
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ]);
    }

    #[Test]
    public function it_deletes_bank_by_id()
    {
        // Arrange
        $banks = Bank::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $bank = $banks->first(); // Get the first bank from the collection

        // Act
        $result = $this->repository->deleteById($bank->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('banks', [
            'id' => $bank->id,
        ]);
    }

    #[Test]
    public function it_throws_exception_when_deleting_nonexistent_bank_by_id()
    {
        // Arrange
        $nonExistentId = 9999;

        // Assert & Act
        $this->expectException(\Exception::class);
        $this->expectException(ModelNotFoundException::class);
        $this->repository->deleteById($nonExistentId);
    }

    #[Test]
    public function it_deletes_bank_model()
    {
        // Arrange
        $banks = Bank::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);
        $bank = $banks->first(); // Get the first bank from the collection

        // Act
        $result = $this->repository->delete($bank);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('banks', [
            'id' => $bank->id,
        ]);
    }

    #[Test]
    public function it_respects_user_isolation_for_banks()
    {
        // Arrange:
        // Create bank for current user
        $userBank = Bank::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create bank for another user
        $anotherUser = User::factory()->create();
        $anotherUserBank = Bank::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $result = $this->repository->getAllForUser();

        // Assert
        $this->assertContains($userBank->id, $result->pluck('id'));
        $this->assertNotContains($anotherUserBank->id, $result->pluck('id'));
    }
}
