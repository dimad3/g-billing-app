<?php

namespace Database\Factories\User;

use App\Models\User\Agent;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Agent::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        $firstName = fake()->firstName($gender);
        $lastName = fake()->lastName($gender);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'position' => fake()->jobTitle,
            'email' => fake()->unique()->safeEmail,
            'role' => fake()->randomElement(array_keys(config('static_data.roles'))),
        ];
    }
}
