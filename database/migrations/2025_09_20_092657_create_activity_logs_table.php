<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action'); // e.g., 'created_task', 'updated_project'
            $table->string('description');
            $table->string('model_type')->nullable(); // e.g., 'App\Models\Task'
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('properties')->nullable(); // Store additional data (e.g., old vs new values)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
