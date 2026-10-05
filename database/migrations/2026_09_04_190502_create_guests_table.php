<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('relationship', ['friend', 'family', 'colleague', 'church', 'neighbor', 'other'])->default('friend');
            $table->string('relationship_other')->nullable();
            $table->enum('supporting', ['bride', 'groom', 'both'])->default('both');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('invite_code', 32)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
