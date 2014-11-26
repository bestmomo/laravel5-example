<?php namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;

use App\Gestion\UserGestion;
use Illuminate\Session\SessionManager;

/**
 * @Middleware("guest", except={"getLogout"})
 */
class AuthController extends Controller {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Show the application registration form.
	 *
	 * @Get("auth/register")
	 *
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return Response
	 */
	public function getRegister(
		UserGestion $user_gestion)
	{
		return view('front.auth.register')->withStatut($user_gestion->getStatut());
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @Post("auth/register")
	 *
	 * @param  App\Http\Requests\RegisterRequest  $request
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return Response
	 */
	public function postRegister(
		Requests\Auth\RegisterRequest $request,
		UserGestion $user_gestion)
	{
		// Vérification pot de miel
		if($request->get('user') != '') return redirect('/');

		$user = $user_gestion->store($request->all());

		$this->auth->login($user);

		return redirect('/')->with('ok', trans('front/register.ok'));
	}

	/**
	 * Show the application login form.
	 *
	 * @Get("auth/login")
	 *
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return Response
	 */
	public function getLogin(
		UserGestion $user_gestion)
	{
		return view('front.auth.login')->withStatut($user_gestion->getStatut());
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @Post("auth/login")
	 *
	 * @param  App\Http\Requests\LoginRequest  $request
	 * @param  Illuminate\Session\SessionManager  $session
	 * @return Response
	 */
	public function postLogin(
		Requests\Auth\LoginRequest $request,
		SessionManager $session)
	{
		// Vérification pot de miel
		if($request->get('user') != '') return redirect('/');

		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt($credentials, $request->has('souvenir')))
		{
			$session->put('statut', $this->auth->user()->role->slug);
			return redirect('/');
		}

		return redirect()->back()
		->with('error', trans('front/login.credentials'))
		->withInput($request->only('email'));
	}

	/**
	 * Log the user out of the application.
	 *
	 * @Get("auth/logout")
	 *
	 * @return Response
	 */
	public function getLogout()
	{
		$this->auth->logout();

		return redirect('/');
	}

}
