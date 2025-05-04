<?php

namespace Tests\Feature\Cabinet\User;

use App\Http\Controllers\Cabinet\User\BankController;
use App\Models\User\User;
use App\Models\User\Bank;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BankControllerTest extends TestCase
{
    use DatabaseTransactions;

    private BankController $controller;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = app(BankController::class);

        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    #[Test]
    public function it_displays_bank_index_page_with_paginated_banks()
    {
        $banks = Bank::factory()->count(15)->for($this->user)->create();

        $response = $this->get(route('cabinet.banks.index'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.banks.index')
            ->assertViewHas('banks')
            ->assertSee('Banks List');
    }

    #[Test]
    public function it_shows_create_bank_form()
    {
        $response = $this->get(route('cabinet.banks.create'));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.banks.create_or_edit')
            ->assertSee('Add New Bank')
            ->assertViewHas(['bank', 'requiredInputs']);
    }

    #[Test]
    public function it_shows_edit_bank_form()
    {
        $bank = Bank::factory()->for($this->user)->create();

        $response = $this->get(route('cabinet.banks.edit', $bank));

        $response->assertStatus(200)
            ->assertViewIs('cabinet.banks.create_or_edit')
            ->assertSee($bank->name)
            ->assertSee($bank->bank_code)
            ->assertViewHas(['bank', 'requiredInputs']);
    }

    #[Test]
    public function it_stores_new_bank_successfully()
    {
        $data = [
            'name' => 'bank name',
            'bank_code' => 'b_code',
        ];

        $response = $this->post(route('cabinet.banks.store'), $data);

        $response->assertRedirect(route('cabinet.banks.index'))
            ->assertSessionHas('success', 'Bank created successfully.');

        $this->assertDatabaseHas('banks', [
            'name' => 'bank name',
            'bank_code' => 'b_code',
        ]);
    }

    #[Test]
    public function it_updates_existing_bank_successfully()
    {
        $bank = Bank::factory()->for($this->user)->create();

        $updatedData = [
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ];

        $response = $this->put(route('cabinet.banks.update', $bank), $updatedData);

        $response->assertRedirect(route('cabinet.banks.index'))
            ->assertSessionHas('success', 'Bank updated successfully.');
        $this->assertDatabaseHas('banks', [
            'id' => $bank->id,
            'user_id' => $this->user->id,
            'name' => 'bank name updated',
            'bank_code' => 'b_code_u',
        ]);
    }

    #[Test]
    public function it_deletes_bank_successfully()
    {
        $bank = Bank::factory()->for($this->user)->create();

        $bankClone = $bank->replicate(); // after delete, bank may by deleted too

        $response = $this->delete(route('cabinet.banks.destroy', [$bank]));

        $response->assertRedirect(route('cabinet.banks.index'))
            ->assertSessionHas('success', 'Bank deleted successfully.');

        $this->assertDatabaseMissing('banks', ['id' => $bankClone->id]);
    }

    #[Test]
    public function it_prevents_bank_creation_with_invalid_data()
    {
        $invalidData = [
            'name' => '', // Required field
            'bank_code' => '', // Required field
        ];

        $response = $this->post(route('cabinet.banks.store'), $invalidData);

        $response->assertSessionHasErrors([
            'name',
            'bank_code',
        ]);
    }

    #[Test]
    public function it_prevents_unauthorized_access_to_other_users_banks()
    {
        $otherUser = User::factory()->create();
        $otherUserBank = Bank::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->get(route('cabinet.banks.edit', $otherUserBank));
        $response->assertNotFound();

        $response = $this->put(route('cabinet.banks.update', $otherUserBank));
        $response->assertNotFound();

        $response = $this->delete(route('cabinet.banks.destroy', $otherUserBank));
        $response->assertNotFound();
    }
}
