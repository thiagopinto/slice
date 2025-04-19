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
        Schema::create('vss_115_recap_liquidacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->string('tipo_transacao')->nullable(); // Adicionado nullable
            $table->decimal('creditos', 18, 2)->nullable(); // Adicionado nullable
            $table->decimal('debitos', 18, 2)->nullable(); // Adicionado nullable
            $table->decimal('total', 18, 2)->nullable(); // Adicionado nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vss_115_recap_liquidacao');
    }
};
