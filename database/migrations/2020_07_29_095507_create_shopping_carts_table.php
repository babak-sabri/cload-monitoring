<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('shopping_carts', function (Blueprint $table) {
			$table->id('shopping_cart_id');
			$table->bigInteger('user_id')->unsigned();
			$table->bigInteger('package_id')->unsigned()->default(0);
			$table->double('total_price')->unsigned();
			$table->bigInteger('updated_at')->unsigned();
			$table->bigInteger('created_at')->unsigned();
			
			$table->index('user_id');
			$table->index('package_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('shopping_carts');
	}
}
