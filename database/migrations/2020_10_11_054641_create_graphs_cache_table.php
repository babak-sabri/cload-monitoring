<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGraphsCacheTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graphs_cache', function (Blueprint $table) {
            $table->id('graph_cache_id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('graphid')->unsigned();
            $table->bigInteger('templateid')->unsigned()->default(0);
            $table->string('graph_name', 255);
            $table->string('monitoring_graph_name', 255);
            $table->string('template_name', 255)->nullable();
			
            $table->index('user_id');
            $table->index('graphid');
            $table->index(['user_id', 'hostid']);
			
			$table
				->foreign('user_id')
				->references('id')
				->on('users')
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
        Schema::dropIfExists('graphs_cache');
    }
}
