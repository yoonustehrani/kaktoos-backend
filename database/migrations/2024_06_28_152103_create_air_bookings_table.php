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
            $table->boolean('is_webfare');
            $table->string('parto_unique_id')->nullable();
            $table->foreignIdFor(User::class)->index();
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
