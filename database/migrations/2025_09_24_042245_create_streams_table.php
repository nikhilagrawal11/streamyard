<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['scheduled', 'live', 'ended', 'recording'])->default('scheduled');
            $table->enum('type', ['live', 'pre_recorded'])->default('live');
            $table->string('stream_key')->unique();
            $table->string('rtmp_url')->nullable();
            $table->string('playback_url')->nullable();
            $table->string('recording_path')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('viewer_count')->default(0);
            $table->integer('max_participants')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('streams');
    }
}
