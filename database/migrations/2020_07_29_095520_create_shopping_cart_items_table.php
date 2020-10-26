<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_cart_items', function (Blueprint $table) {
            $table->id('shopping_cart_item_id');
            $table->bigInteger('shopping_cart_id')->unsigned();
            $table->integer('product_id');
            $table->bigInteger('product_count')->unsigned()->nullable();
            $table->double('price')->unsigned();
            $table->double('total_price')->unsigned();
			
            $table->index('product_id');
			
			$table
				->foreign('shopping_cart_id')
				->references('shopping_cart_id')
				->on('shopping_carts')
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
        Schema::dropIfExists('shopping_cart_items');
    }
}
