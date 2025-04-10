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
        Schema::create('mlos', function (Blueprint $table) {
            $table->id();
            $table->string('line_belongs_to',50)->nullable();
            $table->string('mlo_code',50);
            $table->text('mlo_details')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->timestamps();

            $table->unique(['line_belongs_to', 'mlo_code'], 'line_belongs_to_mlo_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mlos');
    }
};
