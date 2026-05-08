<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove JSON constraint from config field in orders table
        DB::statement('ALTER TABLE orders MODIFY COLUMN config LONGTEXT NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore JSON constraint (optional - for rollback)
        DB::statement('ALTER TABLE orders MODIFY COLUMN config LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`config`))');
    }
};
