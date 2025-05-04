<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory()
        //     ->count(30)
        //     ->has(Entity::factory()
        //         ->has(EntityBankAccount::factory()->count(rand(0, 5)), 'bankAccounts')) // Create 0-5 accounts for each entity)
        //     ->hasClients(rand(0, 30))
        //     ->hasDocuments(rand(0, 30))
        //     ->create();

        User::factory(3)->create();
    }
}
