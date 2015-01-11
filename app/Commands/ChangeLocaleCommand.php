<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;
use Session;

class ChangeLocaleCommand extends Command implements SelfHandling {

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		Session::set('locale', Session::get('locale') == 'fr' ? 'en' : 'fr');
	}

}
