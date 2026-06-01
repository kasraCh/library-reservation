<?php

namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Models\ReservationsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogReservationActivity implements ShouldQueue
{
    use Queueable;

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
            'user_id' => $event->user->id,
        ]);
    }
}
