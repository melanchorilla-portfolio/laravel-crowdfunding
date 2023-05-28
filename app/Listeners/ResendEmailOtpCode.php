<?php

namespace App\Listeners;

use Mail;
use App\Mail\UserRegenerateOtpCodeMail;
use App\Events\RegeneratedOtpCodeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResendEmailOtpCode implements ShouldQueue
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
     * @param  \App\Events\RegeneratedOtpCodeEvent  $event
     * @return void
     */
    public function handle(RegeneratedOtpCodeEvent $event)
    {
        Mail::to($event->user)->send(new UserRegenerateOtpCodeMail($event->user));
    }
}
