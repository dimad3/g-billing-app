<?php

namespace Database\Seeders;

use App\Models\User\Bank;
use App\Models\Entity\Entity;
use App\Models\Entity\EntityBankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntityBankAccountSeeder extends Seeder
{
    public function run()
    {
        Entity::all()->each(function ($entity) {
            $userId = null;
            if ($entity->entityable_type === 'user') {
                $userId = $entity->entityable_id;
            } elseif ($entity->entityable_type === 'client') {
                $userId = $entity->client->user_id;
            }

            $banks = Bank::where('user_id', $userId)->get()->toArray();

            $selectedBanks = fake()->randomElements($banks, rand(1, 5), true);
            // dd($selectedBanks);

            $bankAccounts = [];

            foreach ($selectedBanks as $bank) {
                $bankAccounts[] = [
                    'entity_id' => $entity->id ?? Entity::factory(),
                    'bank_id' => $bank['id'] ?? Bank::factory(),
                    'bank_account' => fake()->unique()->iban(),
                ];
            }

            EntityBankAccount::insert($bankAccounts);
        });
    }
}
