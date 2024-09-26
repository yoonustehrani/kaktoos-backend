<?php

use App\Models\User;
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
        Schema::create('air_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type');
            $table->unsignedInteger('refund_type');
            $table->string('origin_airport_code');
            $table->string('destination_airport_code');
            $table->dateTime('journey_begins_at');
            $table->dateTime('journey_ends_at')->nullable();
            $table->string('airline_code');
            $table->boolean('is_webfare');
            $table->string('parto_unique_id')->nullable();
            $table->foreignIdFor(User::class)->index();
            $table->string('status');
            $table->timestamp('valid_until');
            $table->timestamps();
            $table->json('meta')->default(json_encode([]));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('air_bookings');
    }
};
