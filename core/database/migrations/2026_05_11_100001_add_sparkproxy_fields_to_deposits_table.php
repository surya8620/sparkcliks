<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Cross-reference back to SparkProxy's payment_requests.ref
            $table->string('sparkproxy_ref', 64)->nullable()->unique()->after('from_api');
            // Webhook delivery tracking
            $table->timestamp('webhook_sent_at')->nullable()->after('sparkproxy_ref');
            $table->tinyInteger('webhook_attempts')->default(0)->after('webhook_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['sparkproxy_ref', 'webhook_sent_at', 'webhook_attempts']);
        });
    }
};
