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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('invoice_amount');
            $table->string('invoice_number');
            $table->string('invoice_status')->default(\App\Enums\InvoiceStatus::pending->value);
            $table->unsignedBigInteger('move')->nullable();
            $table->foreign('move')->references('id')->on('moves')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
