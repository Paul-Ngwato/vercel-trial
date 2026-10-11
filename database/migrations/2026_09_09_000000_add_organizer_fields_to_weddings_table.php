<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->string('organizer_name', 255)->nullable()->after('contact_phone');
            $table->string('organizer_role', 255)->nullable()->after('organizer_name');
            $table->string('organizer_email', 255)->nullable()->after('organizer_role');
            $table->string('organizer_phone', 60)->nullable()->after('organizer_email');
            $table->text('organizer_info')->nullable()->after('organizer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('weddings', function (Blueprint $table) {
            $table->dropColumn([
                'organizer_name', 'organizer_role', 'organizer_email',
                'organizer_phone', 'organizer_info',
            ]);
        });
    }
};
