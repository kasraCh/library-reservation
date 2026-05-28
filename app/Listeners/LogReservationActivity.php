<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Models\ReservationsLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogReservationActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReservationCreated $event): void
    {
        ReservationsLog::create([
            'reservation_id' => $event->reservation->id,
            'book_id' => $event->book->id,
            'user_id' => auth()->user()->id,
        ]);
    }
}
