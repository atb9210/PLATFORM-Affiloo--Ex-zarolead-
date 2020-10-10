<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->bigInteger('tag_id')->unsigned();
            $table->bigInteger('advertiser_id')->unsigned();
            $table->string('country')->nullable();
            $table->timestamps();
            $table->boolean('archived')->default(0);

			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('tag_id')->references('id')->on('tags');
			$table->foreign('advertiser_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
