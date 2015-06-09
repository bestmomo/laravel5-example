<?php namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

class ChangeLocaleCommand extends Job implements SelfHandling
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        session()->set('locale', session('locale') == 'fr' ? 'en' : 'fr');
    }
}
