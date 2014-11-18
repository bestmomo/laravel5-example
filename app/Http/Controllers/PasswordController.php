<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\PasswordBroker;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Gestion\UserGestion;
use Illuminate\View\Factory;
	
/**
 * @Middleware("guest")
 */
class PasswordController extends Controller {

	/**
	 * The password broker implementation.
	 *
	 * @var PasswordBroker
	 */
	protected $passwords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(PasswordBroker $passwords)
	{
		$this->passwords = $passwords;

		//$this->middleware('guest');
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
	 * @param  Illuminate\Http\Request $request
	 * @param  Illuminate\View\Factory $view
	 * @return Response
	 */
	public function postEmail(
		Request $request,
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
			case PasswordBroker::INVALID_USER:
				return redirect()->back()->with('error', trans($response));

			case PasswordBroker::RESET_LINK_SENT:
				return redirect()->back()->with('status', trans($response));
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
	 * @param  Illuminate\Http\Request $request
	 * @return Response
	 */
	public function postReset(
		Request $request)
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
			case PasswordBroker::INVALID_PASSWORD:
			case PasswordBroker::INVALID_TOKEN:
			case PasswordBroker::INVALID_USER:
				return redirect()->back()->with('error', trans($response))->withInput();

			case PasswordBroker::PASSWORD_RESET:
				return redirect()->to('/')->with('ok', trans('passwords.reset'));
		}
	}

}
