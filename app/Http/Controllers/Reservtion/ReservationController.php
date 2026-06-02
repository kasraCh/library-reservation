<?php

namespace App\Http\Controllers\Reservtion;

use App\Events\ReservationCreated;
use App\Http\Controllers\Controller;
use App\Jobs\SendReservationConfirmation;
use App\Models\Book;
use App\Models\Reservation;
use App\Traits\ApiResponse\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReservationController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $userID = auth()->id();

        $key = 'reservations'.$userID;

        $data = Cache::remember($key, 60, function () use ($userID) {
            return Reservation::where('user_id', $userID)->get()->toArray();
        });

        return $this->success($data, 'Reservations retrieved successfully.');
    }

    public function reservationDetail(Reservation $reservation): JsonResponse
    {
        return $this->success($reservation->toArray(), 'Reservation retrieved successfully.');
    }

    public function reserveBook(Book $book): JsonResponse
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

                    event(new ReservationCreated(auth()->id(), $reservation->id, $book->id));

                    SendReservationConfirmation::dispatch($reservation)->afterCommit();
                });
            });

            return $this->success($reservation, 'Reservation booked successfully.');
        } catch (\RuntimeException $e) {
            return $this->failure(null, $e->getMessage(), [], $e->getCode());
        }
    }

    public function cancelReservation(Reservation $reservation): JsonResponse
    {
        Gate::authorize('cancelReservation', $reservation);

        $reservation->update([
            'status' => 'cancelled',
            'returned_at' => now(),
        ]);

        return $this->success($reservation, 'Reservation cancelled successfully.');
    }

    public function returnReservation(Reservation $reservation): JsonResponse
    {
        Gate::authorize('returnReservation', $reservation);

        $reservation->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        return $this->success($reservation, 'Reservation return successfully.');
    }
}
