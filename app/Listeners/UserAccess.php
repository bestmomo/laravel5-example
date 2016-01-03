<?php

namespace App\Listeners;

use App\Events\UserAccess;

class UserAccess extends ListenerBase
{
    /**
     * Handle the event.
     *
     * @param  UserAccess  $event
     * @return void
     */
    public function handle(UserAccess $event)
    {
        $this->statut->setStatut();
    }
}
