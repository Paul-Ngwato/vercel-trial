<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->text('invitation_message')->nullable()->after('groom_name');
            $table->string('couple_photo_path', 500)->nullable()->after('hero_image_path');
            $table->string('dress_code')->nullable()->after('theme');
            $table->string('rsvp_deadline')->nullable()->after('dress_code');
            $table->text('family_bride_info')->nullable()->after('rsvp_deadline');
            $table->text('family_groom_info')->nullable()->after('family_bride_info');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn([
                'invitation_message', 'couple_photo_path', 'dress_code',
                'rsvp_deadline', 'family_bride_info', 'family_groom_info',
            ]);
        });
    }
};
