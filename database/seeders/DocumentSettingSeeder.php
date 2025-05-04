<?php

namespace Database\Seeders;

use App\Models\User\Agent;
use App\Models\User\DocumentSetting;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::all()->each(function ($user) {
            $agentId = Agent::where('user_id', $user->id)->inRandomOrder()->first()?->id;
            if ($agentId) {
                DocumentSetting::factory()->create([
                    'user_id' => $user->id,
                    'default_agent_id' => $agentId,
                ]);
            }
        });
    }
}
