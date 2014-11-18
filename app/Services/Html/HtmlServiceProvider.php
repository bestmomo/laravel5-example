<?php namespace App\Services\Html;

class HtmlServiceProvider extends \Illuminate\Html\HtmlServiceProvider {

	/**
	 * Register the HTML builder instance.
	 *
	 * @return void
	 */
	protected function registerHtmlBuilder()
	{
		$this->app->bindShared('html', function($app)
		{
			return new \App\Services\Html\HtmlBuilder($app['url']);
		});
	}

	/**
	 * Register the form builder instance.
	 *
	 * @return void
	 */
	protected function registerFormBuilder()
	{
		$this->app->bindShared('form', function($app)
		{
			$form = new \App\Services\Html\FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

			return $form->setSessionStore($app['session.store']);
		});
	}

}
