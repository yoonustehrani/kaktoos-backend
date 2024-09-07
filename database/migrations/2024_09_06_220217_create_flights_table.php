<?php

use App\Models\AirBooking;
use App\Models\Airport;
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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->foreignIdFor(AirBooking::class);
            $table->string('airline_pnr');
            $table->string('departure_airport_code');
            $table->string('departure_terminal')->nullable();
            $table->dateTime('departs_at');
            $table->string('arrival_airport_code');
            $table->string('arrival_terminal')->nullable();
            $table->dateTime('arrives_at');
            $table->string('marketing_airline_code');
            $table->string('operating_airline_code');
            $table->boolean('is_return');
            $table->json('meta')->default(json_encode([]));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
