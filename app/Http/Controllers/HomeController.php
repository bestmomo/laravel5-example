<?php namespace App\Http\Controllers;

use App\Commands\ChangeLocaleCommand;

class HomeController extends Controller {

	/**
	 * Display the home page.
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
	 * @return Response
	 */
	public function missing()
	{
		return view('front.missing');
	}

	/**
	 * Change language.
	 *
	 * @param  Illuminate\Session\SessionManager  $session
	 * @return Response
	 */
	public function language(
		ChangeLocaleCommand $changeLocaleCommand)
	{
		$this->dispatch($changeLocaleCommand);

		return redirect()->back();
	}

}
