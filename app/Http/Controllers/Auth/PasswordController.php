<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

use Illuminate\View\Factory;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\Http\Requests\Auth\EmailPasswordLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;

class PasswordController extends Controller
{

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
	 * @return void
	 */
	public function __construct()
	{
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

        $response = Password::sendResetLink($request->only('email'), function (Message $message) {
            $message->subject(trans('front/password.reset'));
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));

            case Password::INVALID_USER:
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

		$response = Password::reset($credentials, function($user, $password) {
			$this->resetPassword($user, $password);
		});

		switch ($response) {
			case Password::PASSWORD_RESET:
				return redirect()->to('/')->with('ok', trans('passwords.reset'));

			default:
				return redirect()->back()
						->with('error', trans($response))
						->withInput($request->only('email'));
		}
	}

}
