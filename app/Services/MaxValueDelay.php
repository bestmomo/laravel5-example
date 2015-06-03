<?php namespace App\Services;

use Cache;

class MaxValueDelay {

	/**
	 * Time delay in minutes.
	 *
	 * @var int
	 */
	protected $timeRepeat = 4;

	/**
	 * Max repeat.
	 *
	 * @var int
	 */	
	protected $max = 3;

	/**
	 * Add or increment a key in cache.
	 *
	 * @return void
	 */
	public function increment($key)
	{
		if(!Cache::add($key, 1, $this->timeRepeat))
		{
			Cache::increment($key);
		}
	}

	/**
	 * Check for max value.
	 *
	 * @return bool
	 */
	public function check($key)
	{
		return Cache::get($key) == $this->max; 
	}

}