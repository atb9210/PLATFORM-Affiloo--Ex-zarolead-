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
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->bigInteger('product_id')->unsigned();
            $table->string('quantity')->nullable();
			$table->decimal('commission', 9, 2)->default(0.0);
			$table->decimal('sub_commission', 9, 2)->default(0.0);
			$table->bigInteger('network_id')->unsigned();
            $table->bigInteger('order_status_id')->unsigned();
            $table->json('info')->default('[]');
            $table->timestamps();
            $table->boolean('archived')->default(0);
			
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('product_id')->references('id')->on('products');
			$table->foreign('network_id')->references('id')->on('networks');
			$table->foreign('order_status_id')->references('id')->on('order_statuses');
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
