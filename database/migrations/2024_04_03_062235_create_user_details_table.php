<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_basic_id')->length(50);
            $table->string('first_name')->length(120);
            $table->string('last_name')->length(120);
            $table->string('birthday')->length(120);
            $table->string('phone_number')->length(50);
            $table->string('gender')->length(50);
            $table->text('address');
            $table->string('date')->length(20);
            $table->string('time')->length(20);
            $table->integer('status')->length(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
};
