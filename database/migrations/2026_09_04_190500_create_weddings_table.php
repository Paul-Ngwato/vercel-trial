<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weddings', function (Blueprint $table) {
            $table->id();
            $table->string('bride_name');
            $table->string('groom_name');
            $table->date('wedding_date');
            $table->time('wedding_time')->nullable();
            $table->string('ceremony_venue')->nullable();
            $table->text('ceremony_address')->nullable();
            $table->string('ceremony_map_url')->nullable();
            $table->string('reception_venue')->nullable();
            $table->text('reception_address')->nullable();
            $table->string('reception_map_url')->nullable();
            $table->string('theme')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('hero_image_path', 500)->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_image_path', 500)->nullable();
            $table->string('primary_color', 7)->default('#1a3c2a');
            $table->string('secondary_color', 7)->default('#c9a84c');
            $table->string('bg_color', 7)->default('#fdf8f0');
            $table->string('accent_color', 7)->default('#f0e0d0');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weddings');
    }
};
