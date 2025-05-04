<?php

namespace Database\Factories\User;

use App\Models\User\Agent;
use App\Models\User\DocumentSetting;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentSettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = DocumentSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::inRandomOrder()->first()->id ?? User::factory();
        $defaultAgentId = Agent::where('user_id', $userId)->inRandomOrder()->first()->id ?? Agent::factory();

        return [
            'user_id' => $userId,
            'number_prefix' => fake()->randomElement(['INV-', 'SW-', 'BIL-']),
            'next_number' => rand(10, 10000),
            'default_agent_id' => $defaultAgentId,
            'default_tax_rate' => fake()->randomElement([21, 12, 0]),
        ];
    }
}
