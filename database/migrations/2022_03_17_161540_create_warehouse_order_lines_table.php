<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_order_lines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouse_order_id')->unsigned();
            $table->bigInteger('warehouse_product_variation_id')->unsigned();
            $table->integer('quantity')->unsigned();
            $table->decimal('unit_price', 9, 2);
            $table->decimal('total_price', 9, 2);
            $table->decimal('discount', 9, 2)->defaul(0.0);
            $table->decimal('final_price', 9, 2);
            $table->json('attributes')->nullable();
            $table->timestamps();

			$table->foreign('warehouse_order_id')->references('id')->on('warehouse_orders');
			$table->foreign('warehouse_product_variation_id')->references('id')->on('warehouse_product_variations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_order_lines');
    }
}
