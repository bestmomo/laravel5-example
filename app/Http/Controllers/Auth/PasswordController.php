<?php namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\View\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\Http\Requests\Auth\EmailPasswordLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param \Illuminate\Contracts\Auth\Guard $auth
	 * @param \Illuminate\Contracts\Auth\PasswordBroker $passwords
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;

		$this->middleware('guest');
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @param  EmailPasswordLinkRequest  $request
	 * @param  Illuminate\View\Factory $view
	 * @return Response
	 */
	public function postEmail(
		EmailPasswordLinkRequest $request,
		Factory $view)
	{
		$view->composer('emails.auth.password', function($view) {
            $view->with([
                'title'   => trans('front/password.email-title'),
                'intro'   => trans('front/password.email-intro'),
                'link'    => trans('front/password.email-link'),
                'expire'  => trans('front/password.email-expire'),
                'minutes' => trans('front/password.minutes'),
            ]);
        });

    	switch ($response = $this->passwords->sendResetLink($request->only('email'), function($message)
		{
			$message->subject(trans('front/password.reset'));
		}))
		{
			case PasswordBroker::RESET_LINK_SENT:
				return redirect()->back()->with('status', trans($response));

			case PasswordBroker::INVALID_USER:
				return redirect()->back()->with('error', trans($response));
		}
	}

	/**
	 * Reset the given user's password.
	 * 
	 * @param  ResetPasswordRequest  $request
	 * @return Response
	 */
	public function postReset(ResetPasswordRequest $request)
	{
		$credentials = $request->only(
			'email', 'password', 'password_confirmation', 'token'
		);

		$response = $this->passwords->reset($credentials, function($user, $password)
		{
			$user->password = bcrypt($password);

			$user->save();
		});

		switch ($response)
		{
			case PasswordBroker::PASSWORD_RESET:
				return redirect()->to('/')->with('ok', trans('passwords.reset'));

			default:
				return redirect()->back()->with('error', trans($response))->withInput();
		}
	}

}
