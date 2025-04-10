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
        Schema::create('export_datas', function (Blueprint $table) {
            $table->id();
            $table->string('mlo');
            $table->integer('20ft')->nullable()->unsigned();
            $table->integer('40ft')->nullable()->unsigned();
            $table->integer('45ft')->nullable()->unsigned();
            $table->integer('20R')->nullable()->unsigned();
            $table->integer('40R')->nullable()->unsigned();
            $table->string('commodity');
            $table->string('pod');
            $table->string('trade');
            $table->string('port_code');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_datas');
    }
};
