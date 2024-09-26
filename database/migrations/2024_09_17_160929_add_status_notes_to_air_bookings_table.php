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
        Schema::table('air_bookings', function (Blueprint $table) {
            $table->dropColumn(['status']);
            $table->unsignedInteger('status');
            $table->text('status_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('air_bookings', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_notes']);
        });
    }
};
