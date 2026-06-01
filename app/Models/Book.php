<?php

namespace App\Models;

use App\Observers\BookObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[ObservedBy(BookObserver::class)]
class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'total_copies',
        'available_copies'
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeFindBook($query, string $search)
    {
        $query->where('title', 'LIKE', "%$search%");
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['search'] ?? null, function ($query) use ($filters) {
                $query->where('title', 'LIKE', "%{$filters['search']}%");
            })
            ->when($filters['isbn'] ?? null, function ($query) use ($filters) {
                $query->where('isbn', 'LIKE', "%{$filters['isbn']}%");
            });
    }
}
