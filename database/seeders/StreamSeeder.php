<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StreamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();

        if ($user)
        {
            $stream = $user->streams()->create([
                'title' => 'Demo Live Stream',
                'description' => 'This is a demonstration stream for testing purposes.',
                'type' => 'live',
                'status' => 'scheduled',
                'scheduled_at' => now()->addHour(),
                'settings' => [
                    'allow_chat' => true,
                    'record_stream' => false,
                    'auto_start' => false,
                ]
            ]);

            // Add host participant
            $stream->participants()->create([
                'user_id' => $user->id,
                'role' => 'host',
                'status' => 'invited',
                'participant_name' => $user->name,
                'participant_email' => $user->email,
            ]);
        }
    }
}
