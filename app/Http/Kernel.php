<?php namespace App\Http;

use Exception;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

	/**
	 * The application's HTTP middleware stack.
	 *
	 * @var array
	 */
	protected $middleware = [
		'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
		'Illuminate\Cookie\Middleware\EncryptCookies',
		'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
		'Illuminate\Session\Middleware\StartSession',
		'Illuminate\View\Middleware\ShareErrorsFromSession',
		'Illuminate\Foundation\Http\Middleware\VerifyCsrfToken',
	];

	/**
	 * Handle an incoming HTTP request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function handle($request)
	{
		try
		{
			return parent::handle($request);
		}
		catch(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e)
		{
			//$this->reportException($e);
			
			return response()->view('front.missing', [], 404);
		}
		catch (Exception $e)
		{
			$this->reportException($e);

			return $this->renderException($request, $e);
		}
	}

}
