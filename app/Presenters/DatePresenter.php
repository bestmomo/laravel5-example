<?php namespace App\Presenters;

use Carbon\Carbon;

trait DatePresenter {

	/**
	 * Format created_at attribute
	 *
	 * @param Carbon  $date
	 * @return string
	 */
	public function getCreatedAtAttribute($date)
	{
		return Carbon::parse($date)->format($this->getFormat());
	}

	/**
	 * Format updated_at attribute
	 *
     * @param Carbon  $date
	 * @return string
	 */
	public function getUpdatedAtAttribute($date)
	{
	  return Carbon::parse($date)->format($this->getFormat());
	}

	/**
	 * Format date
	 *
	 * @return string
	 */
	private function getFormat()
	{
	  return config('app.locale') == 'fr' ? 'd-m-Y' : 'm-d-Y';
	}

}