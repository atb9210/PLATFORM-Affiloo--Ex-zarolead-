<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogisticsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_logistics_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('logistics_system_id')->unsigned();
            $table->bigInteger('order_id')->unsigned()->nullable();
            $table->json('info');
            $table->string('status')->default('created');
            $table->timestamps();

			$table->foreign('logistics_system_id')->references('id')->on('third_logistics_systems');
			$table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logistics_orders');
    }
}
