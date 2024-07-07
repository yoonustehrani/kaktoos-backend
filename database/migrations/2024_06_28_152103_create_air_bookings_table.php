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
            $table->boolean('is_webfare');
            $table->string('parto_unique_id');
            $table->foreignIdFor(User::class)->index();
            /**
             * "Booked" "Pending" "Waitlist" "TicketinProcess" 
             * "Ticketed" "TicketedChanged" "TicketedScheduleChange" "TicketedCancelled" "TicketedVoid" 
             * "Cancelled" "Exception" "Gateway" "Duplicate"
             */
            $table->string('status'); 
            $table->text('status_notes');
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
