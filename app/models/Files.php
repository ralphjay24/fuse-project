<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Files extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'files';
	public $timestamps = false;
	protected $primaryKey = 'file_id';
	protected $fillable = array('file_id','file_name','file_type','file_size','file_modified','location','parent_id','user_id');

	

}
