<?php

namespace App\Listeners;

use App\Events\UserAccess as UserAccessEvent;

class UserAccess extends ListenerBase
{
    /**
     * Handle the event.
     *
     * @param  UserAccess  $event
     * @return void
     */
    public function handle(UserAccessEvent $event)
    {
        $this->statut->setStatut();
    }
}
