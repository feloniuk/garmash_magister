<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials_order_products', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->timestamps();

            $table->foreignId('order_id')->references('id')->on('materials_orders')->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials_order_products');
    }
}
