<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->string('user_id',50)->primary();
			$table->string('email',50)->unique();
			$table->string('username',20)->unique();
			$table->string('password',200);
			$table->string('firstname',20);
			$table->string('lastname',20);
			$table->string('isGoogle',10)->nullable();
			$table->string('isDropbox',10)->nullable();
			$table->string('isBox',10)->nullable();

			
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
	}

}
