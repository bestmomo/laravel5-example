<?php namespace App\Models;

trait NumberVu {

	/**
	 * Get number vu
	 *
	 * @return int
	 */
   public function scopeNumberVu($query)
   {
        return $query->whereVu(0)->count();
   }

}