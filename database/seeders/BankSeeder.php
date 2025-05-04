<?php

namespace Database\Seeders;

use App\Models\User\Bank;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = config('static_data.banks');

        User::all()->each(function ($user) use ($banks) {
            // For each user, create 5-12 banks one by one to ensure uniqueness constraints are respected
            $selectedBanks = fake()->unique()->randomElements($banks, rand(5, 12));
            // dd($selectedBanks);

            $data = [];

            foreach ($selectedBanks as $bank) {
                $data[] = [
                    'user_id' => $user->id,
                    'name' => $bank['name'],
                    'bank_code' => $bank['bank_code'],
                ];
            }

            Bank::insert($data);
        });
    }
}
