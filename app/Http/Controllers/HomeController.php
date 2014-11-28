<?php namespace App\Http\Controllers;

use App\Gestion\UserGestion;
use Illuminate\Session\SessionManager;

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
		$user_gestion->getStatut();
	}

	/**
	 * Display the home page.
	 *
	 * @Get("/", as="home")
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('front.index');
	}

	/**
	 * Display the missing page (404).
	 *
	 * @Get("/missing")
	 *
	 * @return Response
	 */
	public function missing()
	{
		return view('front.missing');
	}

	/**
	 * Change language.
	 *
	 * @Get("/language")
	 *
	 * @param  Illuminate\Session\SessionManager  $session
	 * @return Response
	 */
	public function language(
		SessionManager $session)
	{
		$session->set('locale', $session->get('locale') == 'fr' ? 'en' : 'fr');
		return redirect()->back();
	}

}
