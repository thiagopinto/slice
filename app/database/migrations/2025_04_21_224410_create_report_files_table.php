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
        Schema::create('report_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('report_source_id')->unsigned();
            $table->foreign('report_source_id')->references('id')->on('report_sources')->onDelete('cascade');
            $table->string('report_type'); // VSS-110, VSS-115, etc
            $table->string('emitter_name')->nullable();
            $table->string('emitter_id')->nullable();
            $table->string('currency')->default('BRL');
            $table->date('report_date');
            $table->date('proc_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_files');
    }
};
