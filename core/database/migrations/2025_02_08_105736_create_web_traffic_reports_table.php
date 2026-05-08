<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebTrafficReportsTable extends Migration
{
    public function up()
    {
        Schema::create('web_traffic_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key
            $table->unsignedBigInteger('order_id'); // Foreign key
            $table->unsignedInteger('category_id'); // Category
            $table->timestamp('created_at')->index(); // Add index
            $table->timestamp('updated_at'); // Add index
            $table->string('ip_address', 45)->nullable(); // Optional IP column
            $table->text('url')->nullable();

            // Add composite index for optimized querying
            $table->index(['user_id', 'category_id', 'created_at'], 'idx_user_category_created');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('web_traffic_reports');
    }
}
