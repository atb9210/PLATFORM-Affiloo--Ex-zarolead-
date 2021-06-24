<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFbAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_fb_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('gologin_id')->nullable()->unique();
            $table->string('dolphin_id')->nullable()->unique();
            $table->json('profile')->nullable();
            $table->string('name');
            $table->bigInteger('product_id')->nullable()->unsigned();
            $table->text('notes')->nullable();
            $table->mediumText('token')->nullable();
            $table->json('info')->nullable();
            $table->enum('status', ['setup', 'run', 'ban'])->default('setup');
            $table->enum('token_status', ['active', 'disabled'])->nullable();
            $table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('fb_accounts');
    }
}
