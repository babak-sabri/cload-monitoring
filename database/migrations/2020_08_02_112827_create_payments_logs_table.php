<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_logs', function (Blueprint $table) {
			$table->id('payment_log_id');
			$table->bigInteger('user_id')->unsigned();
			$table->double('price')->unsigned();
			$table->bigInteger('entity_id')->unsigned()->nullable();
			$table->tinyInteger('pay_for')->unsigned();
			$table->bigInteger('created_at')->unsigned();
			$table->bigInteger('updated_at')->unsigned();
			
			$table->index('user_id');
			$table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments_logs');
    }
}
