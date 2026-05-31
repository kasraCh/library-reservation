<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendReservationConfirmation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Mail::to($user->email)->send(new ReservationConfirmedMail($this->reservation));
    }
}
