<?php

namespace App\Http\Controllers\Reservtion;

use App\Http\Controllers\Controller;
use App\Jobs\SendReservationConfirmation;
use App\Models\Book;
use App\Models\Reservation;
use App\Traits\ApiResponse\ApiResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReservationController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $userID = auth()->id();

        $key = 'reservations'.$userID;

        $data = Cache::remember($key, 60, function () use ($userID) {
            return Reservation::where('user_id', $userID)->get()->toArray();
        });

        return $this->success($data, 'Reservations retrieved successfully.');
    }

    public function reservationDetail(Reservation $reservation)
    {
        return $this->success($reservation->toArray(), 'Reservation retrieved successfully.');
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

            return $this->success($reservation, 'Reservation booked successfully.');
        } catch (\RuntimeException $e) {
            return $this->failure(null, $e->getMessage(), [], $e->getCode());
        }
    }

    public function cancelReservation(Reservation $reservation)
    {
        Gate::authorize('cancelReservation', $reservation);

        $reservation->update([
            'status' => 'cancelled',
            'returned_at' => now(),
        ]);

        return $this->success($reservation, 'Reservation cancelled successfully.');
    }

    public function returnReservation(Reservation $reservation)
    {
        Gate::authorize('returnReservation', $reservation);

        $reservation->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        return $this->success($reservation, 'Reservation return successfully.');
    }
}
