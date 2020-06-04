<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->bigInteger('campaign_id')->unsigned();
            $table->bigInteger('network_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->json('quantity');
            $table->json('upsell')->nullable();
            $table->decimal('total_commission', 9, 2)->default(0.0);
            $table->bigInteger('traffic_source_id')->unsigned();
            $table->json('info')->nullable();
            $table->json('network_params');
            $table->json('plugins')->nullable();
            $table->integer('visits')->unsigned()->default(0);
            $table->integer('leads')->unsigned()->default(0);
            $table->decimal('total_cost', 9, 2)->default(0.0);
            $table->timestamps();
            $table->boolean('archived')->default(0);

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('campaign_id')->references('id')->on('campaigns');
			$table->foreign('network_id')->references('id')->on('networks');
			$table->foreign('product_id')->references('id')->on('products');
			$table->foreign('traffic_source_id')->references('id')->on('traffic_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');
    }
}
