<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QuotaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('quota', function(Blueprint $table)
		{
			$table->string('user_id',50)->unique();
			$table->string('google_total',50)->nullable();
			$table->string('google_used',50)->nullable();
			$table->string('dbox_total',50)->nullable();
			$table->string('dbox_used',50)->nullable();
			$table->string('box_total',50)->nullable();
			$table->string('box_used',50)->nullable();

		});

		Schema::table('quota', function(Blueprint $table)
    	{
        	$table->foreign('user_id')
      		->references('user_id')
      		->on('users')
      		->onDelete('cascade');
    	});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('quota');
	}

}
