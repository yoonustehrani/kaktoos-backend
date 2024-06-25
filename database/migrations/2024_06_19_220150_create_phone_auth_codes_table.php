<?php

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
        Schema::create('phone_auth_codes', function (Blueprint $table) {
            $table->string('phone_number');
            $table->ipAddress('ip');
            $table->integer('attempts')->unsigned();
            $table->string('code');
            $table->timestamps();
            $table->primary(['phone_number', 'ip']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_auth_codes');
    }
};
