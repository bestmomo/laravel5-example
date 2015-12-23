<?php

namespace App\Jobs;

use App\Jobs\Job;
use Request;

class SetLocale extends Job
{
    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
		
        if(!session()->has('locale'))
        {
            session()->put('locale', Request::getPreferredLanguage( config('app.languages') ));
        }

        app()->setLocale(session('locale'));
    }
}
