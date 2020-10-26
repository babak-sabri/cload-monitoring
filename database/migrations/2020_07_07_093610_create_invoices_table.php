<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices', function (Blueprint $table) {
			$table->id('invoice_id');
			$table->integer('user_id')->unsigned();
			$table->text('description')->nullable();
			$table->double('amount')->unsigned();
			$table->bigInteger('payed_at')->nullable()->unsigned();
			$table->string('pay_type', 50)->nullable();
			$table->string('tracking_code')->nullable();
			$table->bigInteger('updated_at')->unsigned();
			$table->bigInteger('created_at')->unsigned();

			$table->index('user_id');
			$table->index('payed_at');
			$table->index('pay_type');
			$table->index('tracking_code');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('invoices');
	}
}
