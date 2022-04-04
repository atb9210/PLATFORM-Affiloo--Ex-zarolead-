<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_leads', function (Blueprint $table) {
            $table->id();
            $table->text('hash')->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->enum('status', \App\WarehouseLead::STATUSES)->default('unprocessed');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('zone')->nullable();
            $table->string('country')->nullable();
            $table->json('info')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);

			$table->foreign('customer_id')->references('id')->on('customers');
			$table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_leads');
    }
}
