<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmedMail;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendReservationConfirmation implements ShouldQueue
{
    use Queueable;

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function handle(): void
    {
        $reservation = $this->reservation->load('user', 'book');

        Mail::to($reservation->user->email)
            ->send(new ReservationConfirmedMail($reservation));
    }

}
