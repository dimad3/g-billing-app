<?php

namespace Database\Factories\Document;

use App\Models\User\Agent;
use App\Models\User\Client;
use App\Models\Document\Document;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = User::inRandomOrder()->first()->id ?? User::factory();
        $clientId = Client::where('user_id', $userId)->inRandomOrder()->first()->id ?? Client::factory();
        ($agentId = Agent::where('user_id', $userId)->inRandomOrder()->first()->id ?? Agent::factory());
        $documentType =  fake()->randomElement(array_keys(config('static_data.document_types')));

        $documentDate = Carbon::instance(fake()->dateTimeBetween('-7 days', 'now'));
        $dueDate = $documentDate->copy()->addDays(rand(7, 30))->toDateString();
        $deliveryDate = ($documentType == 'goods_del_doc') ?
            $documentDate->copy()->addDays(rand(0, 5))->toDateString() : null;
        $documentDate = $documentDate->toDateString();

        $totalDiscount = fake()->optional(30, 0)->randomFloat(2, 30, 1000);
        $totalNetAmount = fake()->randomFloat(2, 300, 5000);
        $totalVat = round($totalNetAmount * 0.21, 2);
        $documentTotal = $totalNetAmount + $totalVat;
        $advancePaid = fake()->optional(20, 0)->randomFloat(2, 10, 1000);
        $advancePaid = $advancePaid > $documentTotal ? $documentTotal : $advancePaid;

        return [
            'user_id' => $userId,
            'client_id' =>  $clientId,
            'document_date' => $documentDate,
            'number' => fake()->randomElement(['INV-', 'SW-', 'BIL-']) . fake()->unique()->numerify('#####'),
            'document_type' => $documentType,
            'total_discount' => $totalDiscount,
            'total_net_amount' => $totalNetAmount,
            'total_vat' => $totalVat,
            'document_total' => $documentTotal,
            'advance_paid' => $advancePaid,
            'due_date' => $dueDate,
            'delivery_date' => $deliveryDate,
            'status' => fake()->randomElement(array_keys(config('static_data.document_statuses'))),
            'transaction_description' => fake()->optional(90)->sentence(rand(2, 4), false),
            'tax_note' => fake()->optional(20)->sentence,
            'document_note' => fake()->optional(75)->sentence,
            'agent_id' => $agentId,
            'show_created_by' => fake()->boolean(75),
            'show_signature' => fake()->boolean(75),
        ];
    }
}
