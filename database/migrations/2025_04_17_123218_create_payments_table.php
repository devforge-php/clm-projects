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

            $table->uuid('transaction_id')->unique();
            $table->string('external_payment_id')->nullable()->unique();

            $table->string('type');
            $table->integer('quantity');
            $table->decimal('amount', 10, 2);

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
