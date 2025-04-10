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
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('vessel_name');
            $table->decimal('length_overall', 8, 2)->nullable();
            $table->enum('crane_status', ['G', 'GL'])->nullable();
            $table->unsignedInteger('nominal_capacity')->nullable();
            $table->unsignedBigInteger('imo_no')->nullable();
            $table->timestamps();

            $table->unique(['vessel_name', 'imo_no'], 'vessel_name_imo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};
