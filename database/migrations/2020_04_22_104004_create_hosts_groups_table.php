<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreateHostsGroupsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hosts_groups', function (Blueprint $table) {
			$table->id('group_id');
			$table->string('group_name');
			$table->text('decription')->nullable();
			$table->string('api_group_name');
			$table->integer('user_id')->unsigned();
			NestedSet::columns($table);
			$table->bigInteger('updated_at')->unsigned();
			$table->bigInteger('created_at')->unsigned();
			$table->index('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('hosts_groups');
	}
}
