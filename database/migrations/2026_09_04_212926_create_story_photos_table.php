<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('story_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_milestone_id')->constrained()->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('caption', 500)->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_photos');
    }
};
