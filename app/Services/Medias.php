<?php namespace App\Services;

class Medias  {

	/**
	 * Get the media url for redactor
	 *
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return string
	 */
	public static function getUrl($user_gestion)
	{
		$url = config('medias.url');
		if(session('statut') == 'redac')
		{
			$name = $user_gestion->getName();
			$url .= '?exclusiveFolder=' . $name;
		}		
		return $url;
	}

}