<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'domain')) {
                // 'sparkcliks' = normal SparkCliks payment
                // 'sparkproxy' = initiated from SparkProxy (plan purchase)
                $table->string('domain', 32)->nullable()->default(null)->after('sparkproxy_ref');
            }
        });
    }

    public function down(): void
    {
        // No-op: never drop production columns
    });
    }
};
