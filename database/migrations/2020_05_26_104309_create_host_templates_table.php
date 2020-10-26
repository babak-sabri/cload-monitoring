<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_templates', function (Blueprint $table) {
			$table->id('host_template_id');
			$table->bigInteger('hostid')->unsigned();
			$table->integer('template_id')->unsigned();
			
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
        Schema::dropIfExists('host_templates');
    }
}
