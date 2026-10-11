<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Uploaded files for hosts with a read-only filesystem (Vercel):
 * the public disk with PUBLIC_DISK=db stores bytes here instead of disk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_storage', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500)->unique();
            $table->binary('contents');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_storage');
    }
};
