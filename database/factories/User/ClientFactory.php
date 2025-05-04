<?php

namespace Database\Factories\User;

use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'email' => fake()->unique()->safeEmail,
            'due_days' => fake()->randomElement([7, 14, 30]),
            'discount_rate' => fake()->randomFloat(2, 5, 20),
        ];
    }
}
