<?php namespace App\Http\Middleware;

use Closure, Session;
use Illuminate\Contracts\Routing\Middleware;

class App implements Middleware {

	/**
	 * The availables languages.
	 *
	 * @array $languages
	 */
	protected $languages = ['en','fr'];

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(!Session::has('locale'))
		{
			Session::put('locale', $request->getPreferredLanguage($this->languages));
		}

		app()->setLocale(Session::get('locale'));

		return $next($request);
	}

}
