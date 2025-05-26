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
        Schema::create('vessel_turn_arounds', function (Blueprint $table) {
            $table->id();
            $table->string('vessel_name');
            $table->string('jetty');
            $table->enum('crane_status', ['G', 'GL']);
            $table->dateTime('eta');
            $table->dateTime('berth_time');
            $table->dateTime('sail_time');
            $table->integer('oa_stay');
            $table->integer('berth_stay');
            $table->integer('total_stay');
            $table->string('operator');
            $table->integer('import_ldn_teu')->default(0);
            $table->integer('import_mty_teu')->default(0);
            $table->integer('export_ldn_teu')->default(0);
            $table->integer('export_mty_teu')->default(0);
            $table->integer('total_box')->default(0);
            $table->integer('total_teu')->default(0);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vesssel_turn_arounds');
    }
};
