<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmedMail;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDueDateReminder implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $reservation_id)
    {
        $this->reservation_id = $reservation_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reservation = Reservation::with(['user', 'book'])->findOrFail($this->reservation_id);

        Mail::to($reservation->user->email)
            ->send(new ReservationConfirmedMail($reservation));
    }
}
