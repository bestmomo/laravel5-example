<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Events\Dispatcher;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\UserRepository;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
		$this->middleware('guest', ['except' => 'getLogout']);
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param  App\Http\Requests\LoginRequest  $request
	 * @param  Illuminate\Events\Dispatcher  $event
	 * @return Response
	 */
	public function postLogin(
		LoginRequest $request,
		Dispatcher $event)
	{
		// Vérification pot de miel
		if($request->get('user') != '') return redirect('/');

		$credentials = $request->only('email', 'password');

		if ($this->auth->attempt($credentials, $request->has('souvenir')))
		{
			$event->fire('user.login', [$this->auth->user()]);
			return redirect('/');
		}

		return redirect('/auth/login')
		->with('error', trans('front/login.credentials'))
		->withInput($request->only('email'));
	}

	/**
	 * Log the user out of the application.
	 *
	 * @param  Illuminate\Events\Dispatcher  $event
	 * @return Response
	 */
	public function getLogout(
		Dispatcher $event)
	{
		$this->auth->logout();
		$event->fire('user.logout');
		return redirect('/');
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  App\Http\Requests\RegisterRequest  $request
	 * @param  App\Repositories\UserRepository $user_gestion
	 * @param  Illuminate\Events\Dispatcher  $event
	 * @return Response
	 */
	public function postRegister(
		RegisterRequest $request,
		UserRepository $user_gestion,
		Dispatcher $event)
	{
		// Vérification pot de miel
		if($request->get('user') != '') return redirect('/');

		$user = $user_gestion->store($request->all());

		$this->auth->login($user);
		$event->fire('user.login', $user);

		return redirect('/')->with('ok', trans('front/register.ok'));
	}

}
