<?php namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {

	/**
	 * This namespace is applied to the controller routes in your routes file.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * The controllers to scan for route annotations.
	 *
	 * @var array
	 */
	protected $scan = [
		'App\Http\Controllers\HomeController',
		'App\Http\Controllers\UserController',
		'App\Http\Controllers\AdminController',
		'App\Http\Controllers\ContactController',
		'App\Http\Controllers\BlogController',
		'App\Http\Controllers\CommentController',
		'App\Http\Controllers\Auth\AuthController',
		'App\Http\Controllers\Auth\PasswordController',
	];

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router  $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		parent::boot($router);

		//
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		//$this->loadRoutesFrom(app_path('Http/routes.php'));
	}

}
