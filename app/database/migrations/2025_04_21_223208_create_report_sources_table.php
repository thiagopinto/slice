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
        Schema::create('report_sources', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');   // Ex: EP747_20240705.TXT
            $table->string('file_path');   // Ex: storage/reports/EP747_20240705.TXT
            $table->date('imported_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_sources');
    }
};
