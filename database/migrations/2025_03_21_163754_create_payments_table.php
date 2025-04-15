<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Click’dan keladigan payment_id
            $table->string('click_payment_id')->unique();

            $table->string('type');     // gold, silver, diamond
            $table->integer('quantity'); // Nechta tanga
            $table->decimal('amount', 10, 2); // To‘lov summasi

            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'error'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
