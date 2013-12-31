<?php

use Illuminate\Database\Migrations\Migration;

class CreateFavlocsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('favlocs', function($table) {
			$table->engine = 'InnoDB';
			$table->string('id')->unique();

			$table->string('name', 255);
			$table->float('lat');
			$table->float('lng');
			$table->string('address', 255);

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('favlocs');
	}

}