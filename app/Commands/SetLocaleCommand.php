<?php namespace App\Commands;

use App\Commands\Command;

use Session, Request;

use Illuminate\Contracts\Bus\SelfHandling;

class SetLocaleCommand extends Command implements SelfHandling {

	/**
	 * The availables languages.
	 *
	 * @array $languages
	 */
	protected $languages = ['en','fr'];

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		if(!Session::has('locale'))
		{
			Session::put('locale', Request::getPreferredLanguage($this->languages));
		}

		app()->setLocale(Session::get('locale'));
	}

}
