<?php namespace App\Http\Middleware;

use Closure;

use App\Commands\SetLocaleCommand;

use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Events\Dispatcher as EventDispatcher;

class App {

	/**
	 * The command bus.
	 *
	 * @array $bus
	 */
	protected $bus;

	/**
	 * The event bus.
	 *
	 * @array $event
	 */
	protected $event;

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
	 * @param  Illuminate\Events\Dispatcher $event
	 * @param  App\Commands\SetLocaleCommand $setLocaleCommand
	 * @return void
	*/
	public function __construct(
		BusDispatcher $bus,
		EventDispatcher $event,
		SetLocaleCommand $setLocaleCommand)
	{
		$this->bus = $bus;
		$this->event = $event;
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

		$this->event->fire('user.access');

		return $next($request);
	}

}
