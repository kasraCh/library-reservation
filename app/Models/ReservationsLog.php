<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationsLog extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'book_id',
    ];
}
