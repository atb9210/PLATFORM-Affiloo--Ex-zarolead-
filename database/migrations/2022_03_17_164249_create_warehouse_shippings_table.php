<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_shippings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouse_order_id')->unsigned();
            $table->bigInteger('warehouse_shipping_carrier_id')->unsigned();
            $table->enum('status', \App\WarehouseShipping::STATUSES)->default('unprocessed');
            $table->string('tracking_number')->nullable();
            $table->text('tracking_url')->nullable();
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->datetime('delivered_at')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('postcode');
            $table->string('zone');
            $table->string('country');
            $table->text('notes')->nullable();
            $table->json('info')->nullable();
            $table->boolean('archived')->default(0);

			$table->foreign('warehouse_order_id')->references('id')->on('warehouse_orders');
			$table->foreign('warehouse_shipping_carrier_id')->references('id')->on('warehouse_shipping_carriers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_shippings');
    }
}
