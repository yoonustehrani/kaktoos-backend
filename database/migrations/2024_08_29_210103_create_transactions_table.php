<?php

use App\Models\Order;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->unsignedBigInteger('amount');
            $table->string('gateway_purchase_id');
            $table->foreignIdFor(Order::class)->constrained();
            $table->unsignedInteger('status')->nullable();
            $table->text('status_notes')->nullable();
            $table->ipAddress('payer_ip')->nullable();
            $table->json('meta')->default(json_encode([]));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
