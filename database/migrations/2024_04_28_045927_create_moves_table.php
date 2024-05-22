<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Enums\MoveStage;

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
            $table->string('move_stage')->default(MoveStage::contacted->value);
            $table->string('lead_source')->default(\App\Enums\LeadSource::offline_marketing->value);
            $table->string('consumer_name')->nullable();
            $table->string('corporate_name')->nullable();
            $table->string('client_email');
            $table->string('moving_from');
            $table->string('moving_to');
            $table->unsignedBigInteger('sales_representative')->nullable();
            $table->foreign('sales_representative')->references('id')->on('users');
            $table->unsignedBigInteger('branch')->nullable();
            $table->foreign('branch')->references('id')->on('branches')->onDelete('cascade');
            $table->string('invoiced_amount')->nullable();
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
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
