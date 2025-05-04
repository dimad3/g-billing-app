<?php

namespace Database\Factories\Document;

use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = DocumentItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $chanceOfGettingTrue = fake()->boolean(90); // 90% chance to generate true
        $taxRate = $chanceOfGettingTrue ? 21 : fake()->randomElement([0, 12]);

        return [
            'document_id' => Document::inRandomOrder()->first()->id ?? Document::factory(),
            'name' => fake()->word,
            'unit' => fake()->randomElement(['kg', 'gb.', 'm2', 'm3', 'diena', 'mēnesis', '-']),
            'quantity' => fake()->numberBetween(1, 1000),
            'price' => fake()->randomFloat(2, 10, 200),
            'discount_rate' => fake()->randomFloat(2, 0, 20),
            'tax_rate' => $taxRate,
            'note' => fake()->optional(25)->sentence,
        ];
    }
}
