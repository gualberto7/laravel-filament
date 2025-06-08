<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_user', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('gym_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('staff'); // owner, admin, trainer, staff
            $table->json('permissions')->nullable(); // Permisos específicos si se necesitan
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['gym_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_user');
    }
}; 