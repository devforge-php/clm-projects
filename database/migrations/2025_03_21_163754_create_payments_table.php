<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Avval mavjud transaction_id ustunini olib tashlaymiz
            $table->dropColumn('transaction_id');

            // Yangi ustun: Click’dan keladigan payment_id
            $table->string('click_payment_id')->unique()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Aksincha qaytarish
            $table->dropColumn('click_payment_id');
            $table->string('transaction_id')->unique()->after('user_id');
        });
    }
};
