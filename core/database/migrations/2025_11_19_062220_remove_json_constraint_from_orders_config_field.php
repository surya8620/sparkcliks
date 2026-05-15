<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE orders MODIFY COLUMN config LONGTEXT NOT NULL');
        } catch (\Throwable $e) {
            // Already modified or column does not exist — skip
        }
    }
    public function down(): void
    {
        // No-op: never revert production column changes
    }
};