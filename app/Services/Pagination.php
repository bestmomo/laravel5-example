<?php namespace App\Services;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Pagination
{

  /**
   * Create paginator
   *
   * @param  Illuminate\Support\Collection  $collection
   * @param  int     $total
   * @param  int     $perPage
   * @param  string  $appends
   * @return string
   */
  public static function makeLengthAware($collection, $total, $perPage, $appends = null)
  {
    $paginator = new LengthAwarePaginator(
      $collection, 
      $total, 
      $perPage, 
      Paginator::resolveCurrentPage(), 
      ['path' => Paginator::resolveCurrentPath()]
    );

    if($appends) $paginator->appends($appends);

    return str_replace('/?', '?', $paginator->render());
  }
  
}