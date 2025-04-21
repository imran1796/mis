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
            $table->unsignedBigInteger('route_id');
            $table->string('rotation_no',50);
            $table->string('jetty',50);
            $table->string('operator',10);
            $table->string('local_agent',10);
            $table->decimal('effective_capacity',6,2);
            $table->date('arrival_date');
            $table->date('berth_date');
            $table->date('sail_date');
            $table->time('arrival_time')->nullable();
            $table->time('berth_time')->nullable();
            $table->time('sail_time')->nullable();
            $table->date('date');

            $table->foreign('vessel_id')->references('id')->on('vessels')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
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
