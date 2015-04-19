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
		return $this->getDateFormated($date);
	}

	/**
	 * Format updated_at attribute
	 *
	 * @param Carbon  $date
	 * @return string
	 */
	public function getUpdatedAtAttribute($date)
	{
		return $this->getDateFormated($date);
	}

	/**
	 * Format date
	 *
	 * @param Carbon  $date
	 * @return string
	 */
	private function getDateFormated($date)
	{
		return Carbon::parse($date)->format(config('app.locale') == 'fr' ? 'd/m/Y' : 'm/d/Y');
	}

}