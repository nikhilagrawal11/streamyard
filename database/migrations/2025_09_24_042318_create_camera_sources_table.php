<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCameraSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('camera_sources', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('stream_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('source_name');
            $table->enum('source_type', ['webcam', 'screen_share', 'uploaded_video', 'external_rtmp'])->default('webcam');
            $table->string('device_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_primary')->default(false);
            $table->json('settings')->nullable(); // resolution, fps, etc.
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
        Schema::dropIfExists('camera_sources');
    }
}
