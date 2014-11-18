<?php namespace App\Models;

use Carbon\Carbon;

trait DateAttribute {

	/**
	 * Format created_at attribute
	 *
	 * @return string
	 */
	public function getCreatedAtAttribute($date)
	{
		return Carbon::parse($date)->format('d-m-Y');
	}

	/**
	 * Format updated_at attribute
	 *
	 * @return string
	 */
	public function getUpdatedAtAttribute($date)
	{
	  return Carbon::parse($date)->format('d-m-Y');
	}

}