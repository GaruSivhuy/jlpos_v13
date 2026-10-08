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
        Schema::create('service_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount')->nullable();
            $table->integer('service_type')->nullable();
            $table->double('service_fees')->nullable();
            $table->integer('branch_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('user_update')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_fees');
    }
};
