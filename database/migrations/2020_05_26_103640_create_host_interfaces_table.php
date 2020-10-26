<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostInterfacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_interfaces', function (Blueprint $table) {
            $table->id('host_interface_id');
            $table->bigInteger('hostid')->unsigned();
            $table->tinyInteger('type')->unsigned();
            $table->tinyInteger('main')->unsigned();
            $table->tinyInteger('useip')->unsigned();
            $table->string('ip', 20)->nullable();
            $table->string('dns')->nullable();
            $table->bigInteger('port')->unsigned()->nullable();
			
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
        Schema::dropIfExists('host_interfaces');
    }
}
