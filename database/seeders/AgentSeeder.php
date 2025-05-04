<?php

namespace Database\Seeders;

use App\Models\User\Agent;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            Agent::factory(rand(0, 5))->create(['user_id' => $user->id]);
        });
    }
}
