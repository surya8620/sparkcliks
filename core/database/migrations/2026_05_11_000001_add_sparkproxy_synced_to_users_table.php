<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sparkproxy_synced')) {
                $table->tinyInteger('sparkproxy_synced')->default(0)->after('tv');
            }
        });
    }

    public function down(): void
    {
        // No-op: never drop production columns
    });
    }
};
