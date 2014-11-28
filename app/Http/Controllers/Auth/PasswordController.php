<?php namespace App\Http\Controllers\Auth;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Gestion\UserGestion;
use Illuminate\View\Factory;

/**
 * @Middleware("guest")
 */
class PasswordController extends Controller {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * The password broker implementation.
	 *
	 * @var PasswordBroker
	 */
	protected $passwords;

	/**
	 * Create a new password controller instance.
	 *
     * @param Illuminate\Contracts\Auth\Guard  $auth
	 * @param  PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(
		Guard $auth, 
		PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;
	}

	/**
	 * Display the form to request a password reset link.
	 *
	 * @Get("password/email")
	 *
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @return Response
	 */
	public function getEmail(
		UserGestion $user_gestion)
	{
		return view('front.auth.password')->withStatut($user_gestion->getStatut());
	}

	/**
	 * Send a reset link to the given user.
	 *
	 * @Post("password/email")
	 *
	 * @param  EmailPasswordLinkRequest  $request
	 * @param  Illuminate\View\Factory $view
	 * @return Response
	 */
	public function postEmail(
		Requests\Auth\EmailPasswordLinkRequest $request,
		Factory $view)
	{
		// Vérification pot de miel
		if($request->get('user') != '') return redirect('/');

		// Localisation email
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
	 * Display the password reset view for the given token.
	 *
	 * @Get("password/reset/{token}")
	 *
	 * @param  App\Gestion\UserGestion $user_gestion
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset(
		UserGestion $user_gestion,
		$token = null)
	{
		if (is_null($token))
		{
			throw new NotFoundHttpException;
		}

		return view('front.auth.passwordreset')->with(['token' => $token, 'statut' => $user_gestion->getStatut()]);
	}

	/**
	 * Reset the given user's password.
	 *
	 * @Post("password/reset")
	 * 
	 * @param  ResetPasswordRequest  $request
	 * @return Response
	 */
	public function postReset(Requests\Auth\ResetPasswordRequest $request)
	{
    $this->passwords->validator(function($credentials)
		{
		  return strlen($credentials['password']) >= 8;
		});

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
