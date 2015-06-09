<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Contracts\Auth\Guard;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\UserRepository;
use App\Services\MaxValueDelay;

class AuthController extends Controller {

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
	 * @return Response
	 */
	public function postLogin(LoginRequest $request, MaxValueDelay $maxValueDelay)
	{
		$logValue = $request->input('log');

		if($maxValueDelay->check($logValue))
		{
			return redirect('/auth/login')
			->with('error', trans('front/login.maxattempt'));
		}

		$logAccess = filter_var($logValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		
		$credentials = [$logAccess => $logValue, 'password' => $request->input('password')];

		if ($this->auth->attempt($credentials, $request->has('memory')))
		{
			return redirect('/');
		}

		$maxValueDelay->increment($logValue);

		return redirect('/auth/login')
		->with('error', trans('front/login.credentials'))
		->withInput($request->only('email'));
	}


	/**
	 * Handle a registration request for the application.
	 *
	 * @param  App\Http\Requests\RegisterRequest  $request
	 * @param  App\Repositories\UserRepository $user_gestion
	 * @return Response
	 */
	public function postRegister(
		RegisterRequest $request,
		UserRepository $user_gestion)
	{
		$user = $user_gestion->store($request->all());

		$this->auth->login($user);

		return redirect('/')->with('ok', trans('front/register.ok'));
	}

}
