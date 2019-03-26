<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UploadSessionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('upload', function(Blueprint $table)
		{
			$table->string('user_id',50)->unique();
			$table->string('last_session',50)->nullable();
			//$table->string('present_session',100)->nullable();
		});

		Schema::table('upload', function(Blueprint $table)
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
		Schema::drop('upload');
	}

}
