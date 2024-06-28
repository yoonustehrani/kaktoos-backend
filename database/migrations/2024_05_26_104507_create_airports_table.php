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
        Schema::create('airports', function (Blueprint $table) {
            $table->string('name');
            $table->string('name_fa')->nullable();
            $table->string('IATA_code', 4)->unique();
            $table->string('country_code', 2)->nullable();
            $table->string('city_name');
            $table->string('city_name_fa')->nullable();
            // $table->string('location')->nullable();
            // $table->string('location_fa')->nullable();
            $table->boolean('is_international');
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->primary('IATA_code');
            $table->float('rating', 2)->unsigned()->default(0);
            $table->foreign('country_code')->references('code')->on('countries')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airports');
    }
};
