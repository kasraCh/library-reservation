<?php

namespace App\Console\Commands;

use App\Jobs\SendDueDateReminder;
use App\Models\Reservation;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

// #[Signature('app:send-reservation-reminder')]
// #[Description('Command description')]
class SendReservationReminder extends Command
{
    protected $signature = 'reservation:send-reminders';

    protected $description = 'send a email for whose reservation is less than 1 days';

    /**
     * Execute the console command.
     */
    public function handle() 
    {
        $reservations = Reservation::query()
            ->whereDate('due_date', now())
            ->get();

        foreach ($reservations as $reservation) {
            SendDueDateReminder::dispatch($reservation->id);
        }

        $this->info('dispatched '.$reservations->count().' reminders');

        return self::SUCCESS;
    }
}
