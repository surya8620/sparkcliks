<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'sparkproxy_ref')) {
                $table->string('sparkproxy_ref', 64)->nullable()->unique()->after('from_api');
            }
            if (!Schema::hasColumn('deposits', 'webhook_sent_at')) {
                $table->timestamp('webhook_sent_at')->nullable()->after('sparkproxy_ref');
            }
            if (!Schema::hasColumn('deposits', 'webhook_attempts')) {
                $table->tinyInteger('webhook_attempts')->default(0)->after('webhook_sent_at');
            }
        });
    }

    public function down(): void
    {
        // No-op: never drop production columns
    });
    }
};
