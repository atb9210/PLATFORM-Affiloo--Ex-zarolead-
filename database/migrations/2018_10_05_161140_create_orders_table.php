<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
			$table->string('product')->nullable();
            $table->string('quantity')->nullable();
			$table->decimal('commission', 9, 2)->default(0.0);
			$table->string('network');
            $table->integer('order_status_id')->unsigned();
            $table->json('info')->nullable();
            $table->timestamps();
			
			//$table->foreign('order_status_id')->references('id')->on('order_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
