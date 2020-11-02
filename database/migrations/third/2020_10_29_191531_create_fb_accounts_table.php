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
        Schema::create('3rd_fb_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('gologin_id')->nullable()->unique();
            $table->string('dolphin_id')->nullable()->unique();
            $table->json('profile')->nullable();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->string('token')->nullable();
            $table->enum('status', ['setup', 'warm', 'ready', 'run', 'ban'])->default('setup');
            $table->enum('token_status', ['active', 'disabled'])->nullable();
            $table->timestamps();

			$table->foreign('user_id')->references('id')->on('users');
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
