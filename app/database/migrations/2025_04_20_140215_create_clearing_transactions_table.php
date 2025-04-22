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
        Schema::create('clearing_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('arn')->nullable();
            $table->string('slice_code')->nullable();
            $table->decimal('clearing_value', 15, 6)->nullable();
            $table->integer('clearing_currency')->nullable();
            $table->decimal('clearing_commission', 15, 6)->nullable();
            $table->decimal('issuer_exchange_rate', 12, 6)->nullable();
            $table->string('operation_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clearing_transactions');
    }
};
