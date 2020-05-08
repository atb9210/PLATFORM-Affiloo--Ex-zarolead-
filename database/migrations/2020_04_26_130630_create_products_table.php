<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('advertiser_id')->unsigned();
            $table->string('country')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);

			$table->foreign('tag_id')->references('id')->on('tags');
			$table->foreign('advertiser_id')->references('id')->on('advertisers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
    }
}
