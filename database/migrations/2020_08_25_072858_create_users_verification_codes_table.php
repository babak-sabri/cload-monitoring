<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersVerificationCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_verification_codes', function (Blueprint $table) {
            $table->id('verification_id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('verification_code', 50);
            $table->bigInteger('expiration_date')->unsigned();
			
            $table->index('verification_code');
            $table->index('user_id');
			
			$table
				->foreign('user_id')
				->references('id')
				->on('users')
				->onDelete('cascade')
				->onUpdate('cascade')
				;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_verification_codes');
    }
}
