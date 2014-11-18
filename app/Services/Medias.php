<?php namespace App\Services;

class Medias  {

	/**
	 * Get the media url for redactor
	 *
	 * @param  string  $statut
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return string
	 */
	public static function getUrl($statut, $user_gestion)
	{
		$url = config('medias.url');
		if($statut == 'redac')
		{
			$name = $user_gestion->getName();
			$url .= '?exclusiveFolder=' . $name;
		}		
		return $url;
	}

}