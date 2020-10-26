<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostMacrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_macros', function (Blueprint $table) {
			$table->id('host_macro_id');
			$table->bigInteger('hostid')->unsigned();
			$table->string('macro', 100);
			$table->string('macro_value');
			
			$table->index('hostid');

			$table
				->foreign('hostid')
				->references('hostid')
				->on('hosts')
				->onDelete('cascade')
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
        Schema::dropIfExists('host_macros');
    }
}
