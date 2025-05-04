<?php

namespace Database\Seeders;

use App\Models\User\Client;
use App\Models\Entity\Entity;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function ($user) {
            Entity::factory()->create([
                'entityable_id' => $user->id ?? User::factory(),
                'entityable_type' => 'user',
            ]);
        });

        Client::all()->each(function ($client) {
            Entity::factory()->create([
                'entityable_id' => $client->id,
                'entityable_type' => 'client',
            ]);
        });
    }
}
