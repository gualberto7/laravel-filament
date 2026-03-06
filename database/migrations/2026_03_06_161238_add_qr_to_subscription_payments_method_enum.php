<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE subscription_payments MODIFY COLUMN method ENUM('cash', 'card', 'bank_transfer', 'cheque', 'qr') NOT NULL DEFAULT 'cash'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE subscription_payments MODIFY COLUMN method ENUM('cash', 'card', 'bank_transfer', 'cheque') NOT NULL DEFAULT 'cash'");
    }
};
