<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('web_traffic_reports')) return;

        Schema::table('web_traffic_reports', function (Blueprint $table) {
            try { $table->index('created_at'); } catch (\Throwable $e) {}
        });
    }
    public function down()
    {
        // No-op: never drop production indexes
    }
};