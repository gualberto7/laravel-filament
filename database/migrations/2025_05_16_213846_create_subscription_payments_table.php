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
        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'card', 'bank_transfer', 'cheque'])->default('cash');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('paid');
            $table->string('notes')->nullable();
            $table->foreignUuid('subscription_id')->constrained('subscriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
