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
        Schema::create('memberships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('duration'); // in days
            $table->integer('max_installments')->default(1);
            $table->integer('max_checkins')->nullable();
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('is_promo')->default(false);
            $table->date('promo_start_date')->nullable();
            $table->date('promo_end_date')->nullable();
            $table->integer('max_clients')->default(1);
            $table->string('created_by');
            $table->string('updated_by');
            $table->foreignUuid('gym_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
