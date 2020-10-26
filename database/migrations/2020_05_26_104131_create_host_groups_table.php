<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('host_groups', function (Blueprint $table) {
			$table->id('host_group_id');
			$table->bigInteger('hostid')->unsigned();
			$table->bigInteger('group_id')->unsigned();

			$table->index('hostid');

			$table
				->foreign('hostid')
				->references('hostid')
				->on('hosts')
				->onDelete('cascade')
				;
			$table
				->foreign('group_id')
				->references('group_id')
				->on('hosts_groups')
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
        Schema::dropIfExists('host_groups');
    }
}
