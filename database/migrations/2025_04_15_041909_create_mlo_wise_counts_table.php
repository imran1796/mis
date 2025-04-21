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
        Schema::create('mlo_wise_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->date('date');
            $table->string('mlo_code',40);
            $table->enum('type', ['import', 'export']);
            $table->integer('dc20')->default(0);
            $table->integer('dc40')->default(0);
            $table->integer('dc45')->default(0);
            $table->integer('r20')->default(0);
            $table->integer('r40')->default(0);
            $table->integer('mty20')->default(0);
            $table->integer('mty40')->default(0);

            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mlo_wise_counts');
    }
};
