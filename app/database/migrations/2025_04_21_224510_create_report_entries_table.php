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
        Schema::create('report_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('report_file_id')->unsigned();
            $table->foreign('report_file_id')->references('id')->on('report_files')->onDelete('cascade');

            $table->string('report_type');
            $table->string('entry_level')->nullable(); // summary, detail, footer etc
            $table->string('category');
            $table->string('subcategory')->nullable();

            $table->string('dimension_1')->nullable(); // Ex: PURCHASE, ATM CASH
            $table->string('dimension_2')->nullable(); // Ex: RVRSL, DISPUTE FIN

            $table->unsignedBigInteger('count')->nullable();
            $table->decimal('credit_amount', 18, 2)->nullable();
            $table->decimal('debit_amount', 18, 2)->nullable();
            $table->decimal('total_amount', 18, 2)->nullable();

            $table->string('currency')->default('BRL');
            $table->date('report_date');
            $table->date('proc_date')->nullable();
            $table->string('raw_reference')->nullable();
            $table->json('raw_line')->nullable(); // opcional, para guardar linha bruta
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_entries');
    }
};
