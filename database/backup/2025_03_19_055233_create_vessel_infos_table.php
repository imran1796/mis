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
        Schema::create('vessel_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vessel_id');
            $table->string('route',25); //port_code
            $table->string('rotation_no',25);
            $table->string('jetty',25);
            $table->string('operator',25);
            $table->string('local_agent',25);
            $table->unsignedBigInteger('effective_capacity');
            $table->date('arrival_date');
            $table->date('berth_date');
            $table->date('sail_date');
            $table->time('arrival_time')->nullable();
            $table->time('berth_time')->nullable();
            $table->time('sail_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessel_infos');
    }
};
