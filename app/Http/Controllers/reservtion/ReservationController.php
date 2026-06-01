<?php

namespace App\Http\Controllers\reservtion;

use App\Events\ReservationCreated;
use App\Http\Controllers\ApiController;
use App\Jobs\SendReservationConfirmation;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
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

    public function reserveBook(Request $request, Book $book)
    {
        Gate::authorize('reserve', $book);

        $reservation = Reservation::create([
            'user_id' => auth()->id(),
            'book_id' => $book->id,
            'reserved_at' => now(),
            'due_date' => now()->addDays(1),
            'status' => 'active'
        ]);

        $user = auth()->user();

        SendReservationConfirmation::dispatch($reservation);

        event(new ReservationCreated($user,$reservation, $book));

        return $this->successResponse(null, 'Book reserved successfully');
    }

    public function cancelReservation(Reservation $reservation)
    {
        Gate::authorize('cancelReservation', $reservation);

        $reservation->update([
            'status' => 'cancelled'
        ]);

        return $this->successResponse(null, 'Reservation cancelled successfully');

    }

    public function returnReservation(Reservation $reservation)
    {
        Gate::authorize('returnReservation', $reservation);

        $reservation->update([
            'status' => 'returned'
        ]);

        return $this->successResponse(null, 'Book return successfully');
    }
}
