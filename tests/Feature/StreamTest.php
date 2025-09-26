<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Stream;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_stream()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/streams', [
            'title' => 'Test Stream',
            'description' => 'Test Description',
            'type' => 'live',
            'max_participants' => 10,
            'allow_chat' => true,
            'record_stream' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('streams', [
            'title' => 'Test Stream',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_view_own_stream()
    {
        $user = User::factory()->create();
        $stream = Stream::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/streams/{$stream->uuid}");

        $response->assertStatus(200);
        $response->assertSee($stream->title);
    }

    public function test_user_cannot_view_others_private_stream()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $stream = Stream::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/streams/{$stream->uuid}");

        $response->assertStatus(403);
    }
}
