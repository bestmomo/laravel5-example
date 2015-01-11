<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'user.login' => ['App\Services\Statut@setLoginStatut'],
		'user.logout' => ['App\Services\Statut@setVisitorStatut'],
		'user.access' => ['App\Services\Statut@setStatut']
	];

}
