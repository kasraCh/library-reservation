<?php

namespace App\Http\Controllers\Reservtion;

use App\Http\Controllers\ApiController;
use App\Jobs\SendReservationConfirmation;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReservationController extends ApiController
{
    public function index()
    {
        $userID = auth()->id();

        $key = 'reservations'.$userID;

        $data = Cache::remember($key, 60, function () use ($userID) {
            return Reservation::where('user_id', $userID)->get()->toArray();
        });

        return $this->successResponse($data, 'fina all reservations');
    }

    public function reservationDetail(Reservation $reservation)
    {
        return $this->successResponse($reservation, 'found reservation');
    }

    public function reserveBook(Book $book)
    {
        Gate::authorize('reserve', $book);

        try {
            Cache::lock("reservation:book:{$book->id}", 10)->block(5, function () use ($book, &$reservation) {
                DB::transaction(function () use ($book, &$reservation) {

                    $reservation = Reservation::create([
                        'user_id' => auth()->id(),
                        'book_id' => $book->id,
                        'reserved_at' => now(),
                        'due_date' => now()->addDay(),
                        'status' => 'active',
                    ]);

                    SendReservationConfirmation::dispatch($reservation)->afterCommit();
                });
            });

            return $this->successResponse($reservation, 'Book reserved successfully');
        } catch (\RuntimeException $e) {
            return $this->errorResponse(null, $e->getMessage(), 409);
        }
    }

    public function cancelReservation(Reservation $reservation)
    {
        Gate::authorize('cancelReservation', $reservation);

        $reservation->update([
            'status' => 'cancelled',
            'returned_at' => now(),
        ]);

        return $this->successResponse(null, 'Reservation cancelled successfully');

    }

    public function returnReservation(Reservation $reservation)
    {
        Gate::authorize('returnReservation', $reservation);

        $reservation->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        return $this->successResponse(null, 'Book return successfully');
    }
}
