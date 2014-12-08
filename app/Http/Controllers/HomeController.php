<?php namespace App\Http\Controllers;

use App\Gestion\UserGestion;
use Illuminate\Session\SessionManager;

class HomeController extends Controller {

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
