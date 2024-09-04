<?php

use App\Models\Parto\Hotel\HotelBooking;
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
        Schema::create('hotel_booked_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('hotel_booking_id');
            $table->string('name');
            $table->string('meal_type')->nullable();
            $table->string('room_archive_id')->nullable();
            $table->json('meta')->default(json_encode([]));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booked_rooms');
    }
};
