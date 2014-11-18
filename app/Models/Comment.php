<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model  {

	use NumberVu, DateAttribute;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'comments';

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user() 
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function post() 
	{
		return $this->belongsTo('App\Models\Post');
	}

}