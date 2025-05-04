<?php

namespace Database\Factories\Entity;

use App\Models\User\Bank;
use App\Models\Entity\Entity;
use App\Models\Entity\EntityBankAccount;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityBankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = EntityBankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::inRandomOrder()->first()->id ?? User::factory();

        return [
            'entity_id' => Entity::inRandomOrder()->first()->id ?? Entity::factory(),
            // 'entity_id' => Entity::factory(),
            'bank_id' => Bank::where('user_id', $userId)->inRandomOrder()->first()->id ?? Bank::factory(),
            // 'bank_id' => Bank::factory(),
            'bank_account' => fake()->unique()->iban(),
        ];
    }
}
