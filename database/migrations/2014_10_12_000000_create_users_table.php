<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('email')->unique();
			$table->string('cellphone')->unique();
			$table->string('password');
			$table->string('job_title')->nullable();
			$table->string('organization')->nullable();
			$table->string('gender')->default(MALE_GENDER);
			$table->string('language', 10)->default(FARSI_LANGUAGE);
			$table->string('calendar_type', 10)->default(JALALI_CALENDER);
			$table->string('user_type', 10)->default(CUSTOMER_USER);
			$table->string('timezone')->default(config('app-config.default-timezone'));
			$table->string('profile_image')->nullable();
			$table->text('how_to_find')->nullable();
			$table->bigInteger('email_verified_at')->unsigned()->nullable();
			$table->bigInteger('cellphone_verified_at')->unsigned()->nullable();
			$table->rememberToken();
			$table->bigInteger('expiration_date')->nullable()->unsigned();
			$table->bigInteger('updated_at')->unsigned();
			$table->bigInteger('created_at')->unsigned();
			
			$table->index('email');
			$table->index('cellphone');
			$table->index('user_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
