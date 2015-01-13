<?php namespace App\Http\Middleware;

use Closure;

use App\Commands\SetLocaleCommand;

use Illuminate\Bus\Dispatcher as BusDispatcher;

class App {

	/**
	 * The command bus.
	 *
	 * @array $bus
	 */
	protected $bus;

	/**
	 * The command bus.
	 *
	 * @array $bus
	 */
	protected $setLocaleCommand;

	/**
	 * Create a new App instance.
	 *
	 * @param  Illuminate\Bus\Dispatcher $bus
	 * @param  App\Commands\SetLocaleCommand $setLocaleCommand
	 * @return void
	*/
	public function __construct(
		BusDispatcher $bus,
		SetLocaleCommand $setLocaleCommand)
	{
		$this->bus = $bus;
		$this->setLocaleCommand = $setLocaleCommand;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  Illuminate\Http\Request  $request
	 * @param  Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$this->bus->dispatch($this->setLocaleCommand);

		event('user.access');

		return $next($request);
	}

}
