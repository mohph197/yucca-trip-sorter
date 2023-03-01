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
        Schema::create('boarding_cards', function (Blueprint $table) {
            $table->id();
            $table->string('departureLocation');
            $table->string('arrivalLocation');
            $table->string('transportType');
            $table->string('seatNumber')->nullable();
            $table->string('gateNumber')->nullable();
            $table->string('baggageDrop')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
