<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stream_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stream_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['host', 'guest', 'viewer'])->default('guest');
            $table->enum('status', ['invited', 'joined', 'left', 'kicked'])->default('invited');
            $table->string('participant_name');
            $table->string('participant_email')->nullable();
            $table->boolean('camera_enabled')->default(true);
            $table->boolean('microphone_enabled')->default(true);
            $table->boolean('screen_sharing')->default(false);
            $table->json('video_settings')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['stream_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stream_participants');
    }
}
