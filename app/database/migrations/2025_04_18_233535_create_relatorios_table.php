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
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_relatorio');
            $table->date('data_processamento')->nullable();
            $table->date('data_relatorio')->nullable();
            $table->string('entidade_fundo_transferencia')->nullable();
            $table->string('moeda_liquidacao')->nullable();
            $table->string('arquivo_origem')->nullable();
            $table->string('arquivo_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
};
