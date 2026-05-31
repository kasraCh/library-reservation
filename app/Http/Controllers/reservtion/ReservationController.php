<?php

namespace App\Http\Controllers\reservtion;

use App\Events\ReservationCreated;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

class ReservationController extends ApiController
{
    public function index()
    {
        //
    }

    public function reservationDetail(Request $request, Reservation $reservation)
    {
        //
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

        event(new ReservationCreated($reservation, $book));

        return $this->successResponse(null, 'Book reserved successfully');
    }

    public function cancelReservation(Reservation $reservation)
    {
        Gate::authorize('cancelReservation', $reservation);

        $reservation->update([
            'status' => 'cancelled'
        ]);

        return response()->json(['message' => 'ok']);
    }

    public function returnReservation(Request $request, Reservation $reservation)
    {
        //
    }

    public function allReservations(Request $request, Reservation $reservation)
    {
        //
    }
}

