<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TokenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('token', function(Blueprint $table)
		{
			$table->string('user_id',50)->unique();
			$table->string('gtoken',200)->nullable();
			$table->string('dtoken',200)->nullable();
			$table->string('btoken',200)->nullable();
			

		});

		Schema::table('token', function(Blueprint $table)
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
		Schema::drop('token');
	}

}
