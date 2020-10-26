<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->double('price')->unsigned();
            $table->tinyInteger('product_type')->unsigned();
            $table->tinyInteger('product_cat')->unsigned();
            $table->string('entity_id')->nullable();
            $table->bigInteger('updated_at')->unsigned();
			$table->bigInteger('created_at')->unsigned();
			
			$table->index('product_type');
			$table->index('product_cat');
			$table->index('entity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
