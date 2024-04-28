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
        Schema::create('moves', function (Blueprint $table) {
            $table->id();
            $table->date('move_request_received_at')->default(Carbon::now());
            $table->string('lead_source');
            $table->string('consumer_name')->nullable();
            $table->string('corporate_name')->nullable();
            $table->string('contact_information');
            $table->string('moving_from');
            $table->string('moving_to');
            $table->unsignedBigInteger('sales_representative')->change();
            $table->foreign('sales_representative')->references('id')->on('users');
            $table->string('invoiced_amount')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moves');
    }
};