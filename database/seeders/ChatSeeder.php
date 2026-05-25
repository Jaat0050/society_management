<?php

namespace Database\Seeders;

use App\Models\ChatRoom;
use App\Models\Society;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Society::all() as $society) {
            ChatRoom::firstOrCreate(
                ['society_id' => $society->id, 'type' => 'community'],
                ['name' => $society->name.' — Community Forum', 'is_active' => true]
            );

            ChatRoom::firstOrCreate(
                ['society_id' => $society->id, 'type' => 'support'],
                ['name' => 'Help Desk / Admin Support', 'is_active' => true]
            );
        }
    }
}
