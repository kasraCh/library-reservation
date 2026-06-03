<?php

namespace App\Models;

use App\Observers\BookObserver;
use http\QueryString;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

#[ObservedBy(BookObserver::class)]
class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'total_copies',
        'available_copies',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeFindBook($query, string $search): Builder
    {
        return $query->where('title', 'like', "%{$search}%");
    }

}
