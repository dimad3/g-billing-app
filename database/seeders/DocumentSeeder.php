<?php

namespace Database\Seeders;

use App\Models\Document\Document;
use App\Models\User\Agent;
use App\Models\User\Client;
use App\Models\User\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::with(['clients', 'agents'])->get();

        foreach ($users as $user) {
            $user->clients->each(function ($client) use ($user) {
                $agentId = Agent::where('user_id', $user->id)->inRandomOrder()->first()?->id;
                Document::factory(rand(0, 5))->create([
                    'user_id' => $user->id,
                    'client_id' => $client->id,
                    'agent_id' => $agentId,
                ]);
            });
        }
    }
}
