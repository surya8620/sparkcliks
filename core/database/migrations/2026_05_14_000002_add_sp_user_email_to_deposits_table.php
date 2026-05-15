<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add SparkProxy-specific columns to deposits:
 *   - sp_user_email: the real SparkProxy end-user email (user_id is a shadow account)
 *   - webhook_url:   the exact endpoint to POST the payment confirmation to,
 *                   taken from the signed token so it is per-request and not
 *                   dependent on a global env var.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'sp_user_email')) {
                $table->string('sp_user_email', 191)->nullable()->after('domain');
            }
            if (!Schema::hasColumn('deposits', 'webhook_url')) {
                $table->string('webhook_url', 512)->nullable()->after('sp_user_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'webhook_url')) {
                $table->dropColumn('webhook_url');
            }
            if (Schema::hasColumn('deposits', 'sp_user_email')) {
                $table->dropColumn('sp_user_email');
            }
        });
    }
};
