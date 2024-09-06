<?php

use App\Models\AirBooking;
use App\Models\Passenger;
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
        Schema::create('e_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->unsignedInteger('status');
            $table->foreignIdFor(AirBooking::class);
            $table->foreignIdFor(Passenger::class);
            $table->timestamp('issued_at');
            $table->string('airline_pnr');
            $table->boolean('refunded')->default(false);
            $table->unsignedBigInteger('total_refund')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('e_tickets');
    }
};
