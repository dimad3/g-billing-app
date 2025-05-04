<?php

namespace Database\Seeders;

use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            Client::factory(rand(0, 30))->create(['user_id' => $user->id]);
        });
    }
}
