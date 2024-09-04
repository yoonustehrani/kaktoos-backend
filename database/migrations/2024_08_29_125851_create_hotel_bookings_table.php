<?php

use App\Models\Hotel;
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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(Hotel::class);
            $table->string('parto_unique_id')->nullable();
            $table->foreignIdFor(User::class)->index();
            $table->unsignedInteger('status');
            $table->string('supplier')->nullable();
            $table->string('vat_number')->nullable();
            $table->timestamp('payment_valid_until')->nullable();
            $table->boolean('payment_time_extendable')->default(false);
            $table->timestamps();
            $table->json('meta')->default(json_encode([]));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_bookings');
    }
};
