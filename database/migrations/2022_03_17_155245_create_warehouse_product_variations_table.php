<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_product_variations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouse_product_id')->unsigned();
            $table->string('sku')->unique();
            $table->string('name');
            $table->decimal('price', 9, 2);
            $table->integer('quantity')->unsiged()->default(0);
            $table->timestamps();

			$table->foreign('warehouse_product_id')->references('id')->on('warehouse_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_product_variations');
    }
}
