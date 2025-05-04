<?php

namespace Database\Seeders;

use App\Models\User\Bank;
use App\Models\User\Client;
use App\Models\Entity\Entity;
use App\Models\Entity\EntityBankAccount;
use App\Models\User\Agent;
use App\Models\Document\Document;
use App\Models\Document\DocumentItem;
use App\Models\User\DocumentSetting;
use App\Models\User\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\Comment\Doc;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
        // Parent tables first
            UserSeeder::class,
            BankSeeder::class,

            // Then child tables
            ClientSeeder::class,
            EntitySeeder::class,
            EntityBankAccountSeeder::class,

            AgentSeeder::class,
            DocumentSettingSeeder::class,
            DocumentSeeder::class,
            DocumentItemSeeder::class,
        ]);

        // // Parent tables first
        // Bank::factory(12)->create();
        // User::factory(3)->create();
        // Client::factory(50)->create();
        // Entity::factory(53)->create(); // todo: entityable_id not unique
        // EntityBankAccount::factory(100)->create();

        // Agent::factory(10)->create();
        // DocumentSetting::factory(3)->create(); // todo: user_id not unique
        // Document::factory(50)->create();
        // DocumentItem::factory(200)->create();
    }
}
