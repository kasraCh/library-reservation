<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmedMail;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendReservationConfirmation implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array|int $backoff = [10, 30, 60];

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function handle(): void
    {
        //        throw new \Exception('Test queue failure');
        $reservation = $this->reservation->load('user', 'book');

        Mail::to($reservation->user->email)
            ->send(new ReservationConfirmedMail($reservation));
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Reservation confirmation job failed', [
            'reservation_id' => $this->reservation->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
