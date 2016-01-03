<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;

class IsRedactor {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (session('statut') === 'admin' || session('statut') === 'redac')
		{
			return $next($request);
		}
		return new RedirectResponse(url('/'));
	}

}