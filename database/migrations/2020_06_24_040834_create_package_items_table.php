<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_items', function (Blueprint $table) {
            $table->id('package_items_id');
			$table->bigInteger('package_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->integer('count')->unsigned()->default(0);
			$table->integer('entity_id')->unsigned()->default(0);
            $table->index('package_id');
            $table->index('product_id');
			
			$table
				->foreign('package_id')
				->references('package_id')
				->on('packages')
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
        Schema::dropIfExists('package_items');
    }
}
