<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropinProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_dropin_products', function (Blueprint $table) {
            $table->string('sku');
            $table->string('name');
            $table->string('variations')->nullable();
            $table->text('description')->nullable();
            $table->string('category');
            $table->json('images')->nullable();
            $table->decimal('dropshipping_price', 9, 2)->default(0.0);
            $table->decimal('sale_price', 9, 2)->default(0.0);
            $table->decimal('cost', 9, 2)->default(0.0);
            $table->integer('quantity')->unsigned()->default(0);
            $table->timestamps();

            $table->primary('sku');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_dropin_products');
    }
}
