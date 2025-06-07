<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->nullable(); // Asaas payment ID
            $table->decimal('amount', 10, 2);
            $table->string('customer_id')->nullable(); // Asaas customer ID
            $table->string('billing_type'); // BOLETO, CREDIT_CARD, PIX
            $table->string('status')->default('pending'); // pending, success, failed
            $table->text('description')->nullable();
            $table->json('payment_data')->nullable(); // Store additional payment data
            $table->json('response_data')->nullable(); // Store Asaas API response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
