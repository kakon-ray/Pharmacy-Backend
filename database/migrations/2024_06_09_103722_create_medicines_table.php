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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('company_id');
            $table->string('medicine_name')->nullable();
            $table->string('purchase_date')->nullable();
            $table->decimal('purchase_price_pice')->default();
            $table->decimal('purchase_price')->default();
            $table->decimal('selling_price')->default();
            $table->decimal('selling_price_pice')->default();
            $table->string('expired_date')->nullable();
            $table->string('stock')->nullable();
            $table->timestamps();
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicines');
    }
};
