<?php

namespace App\Events;

use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Reservation $reservation, public Book $book)
    {
//        ReservationsLog::create([
//            'reservation_id' => $reservation->id,
//            'book_id' => $book->id,
//            'user_id' => $this->user->id
//        ]);
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return array<int, Channel>
//     */
//    public function broadcastOn(): array
//    {
//        //
//    }
}
