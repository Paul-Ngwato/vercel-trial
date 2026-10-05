<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('churches', function (Blueprint $table) {
            $table->foreignId('wedding_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('wedding_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });

        Schema::table('information_items', function (Blueprint $table) {
            $table->foreignId('wedding_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });

        Schema::table('story_milestones', function (Blueprint $table) {
            $table->foreignId('wedding_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId('wedding_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->foreignId('wedding_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('churches', fn (Blueprint $t) => $t->dropConstrainedForeignId('wedding_id'));
        Schema::table('events', fn (Blueprint $t) => $t->dropConstrainedForeignId('wedding_id'));
        Schema::table('information_items', fn (Blueprint $t) => $t->dropConstrainedForeignId('wedding_id'));
        Schema::table('story_milestones', fn (Blueprint $t) => $t->dropConstrainedForeignId('wedding_id'));
        Schema::table('guests', fn (Blueprint $t) => $t->dropConstrainedForeignId('wedding_id'));
        Schema::table('announcements', fn (Blueprint $t) => $t->dropConstrainedForeignId('wedding_id'));
    }
};
