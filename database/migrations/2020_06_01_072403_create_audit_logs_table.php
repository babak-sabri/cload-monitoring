<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('audit_log_id');
            $table->integer('user_id')->unsigned();
            $table->string('entity_id');
            $table->tinyInteger('resource')->unsigned();
            $table->tinyInteger('action')->unsigned();
            $table->text('details')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('action_time')->unsigned();
			
			$table->index('entity_id');
			$table->index('resource');
			$table->index('action');
			$table->index('action_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}
