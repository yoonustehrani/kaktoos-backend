<?php

use App\Models\HotelBookedRoom;
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
        Schema::create('hotel_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(HotelBookedRoom::class);
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedInteger('title')->nullable();
            $table->unsignedInteger('type');
            $table->unsignedInteger('age')->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_guests');
    }
};
