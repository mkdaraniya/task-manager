<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();        // Full name
            $table->string('avatar')->nullable();      // Path to avatar
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('timezone')->nullable();
            $table->json('social_links')->nullable();  // Store Twitter, LinkedIn etc.
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
