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
            $table->enum('move_stage', ['new_lead', 'contacted', 'survey_scheduled', 'quote_sent', 'negotiations_started',
                'proposal', 'won', 'lost'])->default('new_lead');
            $table->string('consumer_name')->nullable();
            $table->string('corporate_name')->nullable();
            $table->string('contact_information');
            $table->string('moving_from');
            $table->string('moving_to');
            $table->unsignedBigInteger('sales_representative')->nullable();
            $table->foreign('sales_representative')->references('id')->on('users');
            $table->unsignedBigInteger('store')->nullable();
            $table->foreign('store')->references('id')->on('stores');
            $table->unsignedBigInteger('lead')->nullable();
            $table->foreign('lead')->references('id')->on('leads');
            $table->string('invoiced_amount')->nullable();
            $table->text('notes')->nullable();
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
