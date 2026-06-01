<?php

namespace App\Observers;

use App\Models\book;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    /**
     * Handle the book "created" event.
     */
    public function created(book $book): void
    {
        Cache::forget('books.list');
    }

    /**
     * Handle the book "updated" event.
     */
    public function updated(book $book): void
    {
        Cache::forget('books.list');
    }

    /**
     * Handle the book "deleted" event.
     */
    public function deleted(book $book): void
    {
        Cache::forget('books.list');
    }

    /**
     * Handle the book "restored" event.
     */
    public function restored(book $book): void
    {
        //
    }

    /**
     * Handle the book "force deleted" event.
     */
    public function forceDeleted(book $book): void
    {
        //
    }
}
