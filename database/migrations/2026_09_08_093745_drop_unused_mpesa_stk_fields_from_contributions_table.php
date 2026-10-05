<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * These columns were added for a planned M-Pesa Daraja (STK Push) integration
     * that is no longer part of this app. Contributions are now recorded manually
     * by guests and verified by the couple, so these fields are unused.
     */
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn([
                'merchant_request_id',
                'checkout_request_id',
                'mpesa_receipt_number',
                'stk_initiated_at',
                'stk_completed_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->string('merchant_request_id')->nullable()->after('transaction_reference');
            $table->string('checkout_request_id')->nullable()->after('merchant_request_id');
            $table->text('mpesa_receipt_number')->nullable()->after('checkout_request_id');
            $table->timestamp('stk_initiated_at')->nullable()->after('mpesa_receipt_number');
            $table->timestamp('stk_completed_at')->nullable()->after('stk_initiated_at');
        });
    }
};
