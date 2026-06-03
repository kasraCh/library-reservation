<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reservation;

use App\Events\ReservationCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
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
        $userId = auth()->id();

        $key = sprintf('reservations%d', $userId);

        $payload = Cache::remember($key, 60, function () use ($userId) {
            $reservation = Reservation::where('user_id', $userId);

            return ReservationResource::collection($reservation->get())->resolve();
        });

        return $this->success($payload, 'data received successfully');
    }

    public function reservationDetail(Reservation $reservation): JsonResponse
    {
        return $this->success(new ReservationResource($reservation), 'Reservation retrieved successfully.');
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

            return $this->success(new ReservationResource($reservation), 'Reservation booked successfully.');

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

        return $this->success(new ReservationResource($reservation), 'Reservation cancelled successfully.');
    }

    public function returnReservation(Reservation $reservation): JsonResponse
    {
        Gate::authorize('returnReservation', $reservation);

        $reservation->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        return $this->success(new ReservationResource($reservation), 'Reservation return successfully.');
    }
}
