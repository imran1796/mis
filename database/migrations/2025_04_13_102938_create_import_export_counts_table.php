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
        Schema::create('import_export_counts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vessel_info_id');
            $table->enum('type', ['import', 'export']);
            $table->unsignedInteger('dc20')->default(0);
            $table->unsignedInteger('dc40')->default(0);
            $table->unsignedInteger('dc45')->default(0);
            $table->unsignedInteger('r20')->default(0);
            $table->unsignedInteger('r40')->default(0);
            $table->unsignedInteger('mty20')->default(0);
            $table->unsignedInteger('mty40')->default(0);

            $table->foreign('vessel_info_id')->references('id')->on('vessel_infos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_export_counts');
    }
};
