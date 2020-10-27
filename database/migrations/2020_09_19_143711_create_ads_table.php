<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('traffic_source_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->decimal('ctr', 9, 2)->default(0.0);
            $table->decimal('cpc', 9, 2)->default(0.0);
            $table->decimal('cpa', 9, 2)->default(0.0);
            $table->json('info');
            $table->bigInteger('product_media_id')->unsigned();
            $table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('traffic_source_id')->references('id')->on('traffic_sources');
			$table->foreign('product_id')->references('id')->on('products');
			$table->foreign('product_media_id')->references('id')->on('product_medias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
}
