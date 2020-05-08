<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->bigInteger('product_id')->unsigned()->default(0); // TODO: to remove default 0????
            $table->string('quantity')->nullable();
			$table->decimal('commission', 9, 2)->default(0.0);
			$table->decimal('sub_commission', 9, 2)->default(0.0);
			$table->string('network');
            $table->bigInteger('order_status_id')->unsigned();
            $table->json('info')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);
			
			//$table->foreign('order_status_id')->references('id')->on('order_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('orders');
    }
}
