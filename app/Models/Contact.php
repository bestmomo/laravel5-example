<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model  {

	use NumberVu, DateAttribute;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'contacts';

}