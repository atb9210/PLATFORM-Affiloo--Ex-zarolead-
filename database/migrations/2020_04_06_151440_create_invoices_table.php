<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->boolean('uploaded')->default(0);
            $table->boolean('transaction')->default(0);
            $table->string('language')->nullable();
            $table->datetime('invoice_date');
            $table->string('transaction_id')->nullable();
            $table->string('number')->nullable();
            $table->string('status')->nullable();
            $table->decimal('amount', 9, 2)->default(0.0);
            $table->string('currency')->nullable();
            $table->json('info')->nullable();
            $table->string('original_file_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('invoices');
    }
}
