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
        Schema::create('vss_110_resumo_liquidacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relatorio_id')->constrained('relatorios')->onDelete('cascade');
            $table->string('categoria')->nullable(); // Adicionado nullable
            $table->string('tipo_transacao')->nullable(); // Adicionado nullable
            $table->integer('quantidade')->nullable(); // Adicionado nullable
            $table->decimal('valor', 18, 2)->nullable(); // Adicionado nullable
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vss_110_resumo_liquidacao');
    }
};
