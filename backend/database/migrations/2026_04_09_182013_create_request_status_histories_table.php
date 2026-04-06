<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_status_histories', function (Blueprint $table) {
            $table->id();
            $table->string('oldStatus')->nullable();
            $table->string('newStatus');
            $table->timestamp('changedAt')->useCurrent();
            $table->foreignId('purchase_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_status_histories');
    }
};
