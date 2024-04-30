<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('volumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('move_id')->constrained()->onDelete('cascade');
            $table->string('area');
            $table->string('item');
            $table->decimal('size_cubic_meters', 8, 2);
            $table->integer('quantity');
            $table->integer('number_of_boxes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volumes');
    }
};
