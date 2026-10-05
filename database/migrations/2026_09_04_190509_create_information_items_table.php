<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('information_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('icon', 10)->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_items');
    }
};
