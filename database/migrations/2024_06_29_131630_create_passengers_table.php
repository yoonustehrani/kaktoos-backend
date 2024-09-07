<?php

use App\Models\AirBooking;
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
        Schema::create('passengers', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignIdFor(AirBooking::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate(); // air_booking_id
            // $table->foreignIdFor(User::class)->index(); // user_id
            $table->enum('gender', ['male', 'female']); // "Male" "Female"
            $table->string('type'); // "SeniorAdt" "Adt" "Chd" "Inf"
            $table->string('title'); // "Mr" "Mrs" "Ms" "Miss" "Mstr"
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('birthdate');
            $table->string('country_code')->default('IR');
            $table->string('national_code')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expires_on')->nullable();
            $table->date('passport_issued_on')->nullable();
            $table->timestamps();
            $table->foreign('country_code')->references('code')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
