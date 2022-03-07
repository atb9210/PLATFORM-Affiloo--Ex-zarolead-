<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropinShippings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_dropin_shippings', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('postcode');
            $table->text('notes')->nullable();
            $table->decimal('amount', 9, 2)->default(0.0);
            $table->decimal('delivery_price', 9, 2)->default(0.0);
            $table->decimal('tax', 9, 2)->default(0.0);
            $table->enum('payment_method', ['cod', 'online']);
            $table->string('status');
            $table->text('tracking_url')->nullable();
            $table->json('info')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('created_at')->nullable();
            $table->date('updated_at')->nullable();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_dropin_shippings');
    }
}
