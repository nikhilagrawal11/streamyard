<?php

namespace Database\Factories;

use App\Models\Stream;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreamFactory extends Factory
{
    protected $model = Stream::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['scheduled', 'live', 'ended']),
            'type' => $this->faker->randomElement(['live', 'pre_recorded']),
            'max_participants' => $this->faker->numberBetween(2, 20),
            'viewer_count' => $this->faker->numberBetween(0, 100),
            'settings' => [
                'allow_chat' => $this->faker->boolean(),
                'record_stream' => $this->faker->boolean(),
                'auto_start' => $this->faker->boolean(),
            ],
            'scheduled_at' => $this->faker->optional()->dateTimeBetween('+1 hour', '+1 week'),
        ];
    }

    public function live()
    {
        return $this->state([
            'status' => 'live',
            'started_at' => now()->subMinutes($this->faker->numberBetween(5, 60)),
        ]);
    }

    public function ended()
    {
        return $this->state([
            'status' => 'ended',
            'started_at' => now()->subHours(2),
            'ended_at' => now()->subHour(),
        ]);
    }
}
