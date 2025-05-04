<?php

namespace Database\Factories\User;

use App\Models\User\Bank;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User\Bank>
 */
class BankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Bank::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $banks = config('static_data.banks');

        // Generate a user_id for the bank or use the one provided in states
        $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;
        $bank = fake()->randomElement($banks);

        return [
            'user_id' => $userId,
            'name' => $bank['name'],
            'bank_code' => $bank['bank_code'],
        ];
    }
}
