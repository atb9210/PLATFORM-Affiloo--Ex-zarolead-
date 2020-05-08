<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('name');
            $table->string('network');
            $table->bigInteger('product_id')->unsigned();
            $table->string('quantity')->nullable();
            $table->decimal('price', 9, 2)->default(0.0);
            $table->decimal('commission', 9, 2)->default(0.0);
            $table->decimal('sub_commission', 9, 2)->default(0.0);
            $table->decimal('total_commission', 9, 2)->default(0.0);
            $table->bigInteger('traffic_source_id')->unsigned();
            $table->json('info')->nullable();
            $table->integer('visits')->unsigned()->default(0);
            $table->integer('leads')->unsigned()->default(0);
            $table->decimal('total_cost', 9, 2)->default(0.0);
            $table->timestamps();
            $table->boolean('archived')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('forms');
    }
}
