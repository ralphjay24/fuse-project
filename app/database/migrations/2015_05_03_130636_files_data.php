<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FilesData extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('files', function(Blueprint $table)
		{
			$table->string('file_id',250)->primary();
			$table->string('file_name',200);
			$table->string('file_type',20);
			$table->string('file_size',20)->nullable();
			$table->string('file_modified',50);
			$table->string('location',10);
			$table->string('parent_id',250);
			$table->string('user_id',50);
		});

		Schema::table('files', function(Blueprint $table)
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
		Schema::drop('files');
	}

}
