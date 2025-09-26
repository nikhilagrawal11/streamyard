<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VideoUpload;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoUploadFactory extends Factory
{
    protected $model = VideoUpload::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'filename' => $this->faker->uuid() . '.mp4',
            'original_filename' => $this->faker->word() . '.mp4',
            'mime_type' => 'video/mp4',
            'file_size' => $this->faker->numberBetween(1000000, 100000000), // 1MB to 100MB
            'duration' => $this->faker->numberBetween(30, 3600), // 30 seconds to 1 hour
            'status' => $this->faker->randomElement(['processing', 'ready', 'failed']),
        ];
    }

    public function ready()
    {
        return $this->state([
            'status' => 'ready',
        ]);
    }
}
