<?php namespace App\Http\Controllers;

use App\Gestion\UserGestion;

class HomeController extends Controller {

	/**
	 * The UserGestion instance.
	 *
	 * @var App\Gestion\UserGestion
	 */
	protected $user_gestion;

	/**
	 * Create a new HomeController instance.
	 *
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return void
	 */
	public function __construct(
		UserGestion $user_gestion)
	{
		$this->user_gestion = $user_gestion;
	}

	/**
	 * Display the home page.
	 *
	 * @Get("/", as="home")
	 */
	public function index()
	{
		return view('front.index')->withStatut($this->user_gestion->getStatut());
	}

	/**
	 * Display the missing page (404).
	 *
	 * @Get("/missing")
	 */
	public function missing()
	{
		return view('front.missing')->withStatut($this->user_gestion->getStatut());
	}

}
