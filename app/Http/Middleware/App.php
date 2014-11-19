<?php namespace App\Http\Middleware;

use Closure, Session, Config;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Http\RedirectResponse;

class App implements Middleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		Config::set('app.locale', Session::get('locale', function() 
			{ 
				return $request->getPreferredLanguage(['en', 'fr']); 
			})
		);

		return $next($request);
	}

}
