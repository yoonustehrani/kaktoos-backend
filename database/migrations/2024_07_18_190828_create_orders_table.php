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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->morphs('purchasable');
            $table->string('title')->nullable();
            $table->unsignedBigInteger('amount');
            // TODO: amount paid and to be paid (for price change)
            $table->unsignedBigInteger('amount_paid')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->default(json_encode([]));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
