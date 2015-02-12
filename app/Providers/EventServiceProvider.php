<?php namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'auth.login' => ['App\Services\Statut@setLoginStatut'],
		'auth.logout' => ['App\Services\Statut@setVisitorStatut'],
		'user.access' => ['App\Services\Statut@setStatut']
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param \Illuminate\Contracts\Events\Dispatcher $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);
	}

}
