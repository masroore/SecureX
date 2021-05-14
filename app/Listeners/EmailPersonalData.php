<?php

namespace App\Listeners;

use App\Events\RequestPersonalData;
use App\Mail\User\PersonalData;
use App\Notifications\User\PersonalDataRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EmailPersonalData
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RequestPersonalData  $event
     * @return void
     */
    public function handle(RequestPersonalData $event)
    {
        Mail::to($event->user->email)->send(new PersonalData($event->user, $event->time));
    }
}
