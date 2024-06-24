<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RoleEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_local_number')->unique();
            $table->string('phone_country_code');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('firm')->nullable(); // this points to the store Id
            $table->foreign('firm')->references('id')->on('firms')->onDelete('set null');
            $table->unsignedBigInteger('branch')->nullable(); // this points to the store Id
            $table->foreign('branch')->references('id')->on('branches')->onDelete('cascade');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->string('user_type')->default(RoleEnum::sales->value);
            $table->rememberToken();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
