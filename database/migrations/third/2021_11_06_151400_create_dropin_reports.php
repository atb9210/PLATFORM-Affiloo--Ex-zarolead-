<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDropinReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_dropin_reports', function (Blueprint $table) {
            $table->id();
            $table->date('day');
            $table->integer('open_orders')->default(0);
            $table->integer('closed_orders')->default(0);
            $table->decimal('marketing', 9, 2)->default(0.0);
            $table->decimal('shipping', 9, 2)->default(0.0);
            $table->decimal('cogs', 9, 2)->default(0.0);
            $table->decimal('order_value', 9, 2)->default(0.0);
            $table->decimal('revenue', 9, 2)->default(0.0);
            $table->decimal('net_revenue', 9, 2)->default(0.0);
            $table->decimal('vat', 9, 2)->default(0.0);
            $table->decimal('profit', 9, 2)->default(0.0);
            $table->decimal('cdv', 9, 2)->default(0.0);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_dropin_reports');
    }
}
