<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Models\Book;
use App\Traits\ApiResponse\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $books = Cache::remember('books.list', 60, function () {
            return Book::query()->get()->toArray();
        });

        return $this->success($books, 'Books retrieved successfully.');
    }

    public function find(): JsonResponse
    {
        $data = Book::FilterBook(request()->all())->orderBy('created_at', 'desc')->get();

        if (! empty($data)) {
            return $this->failure($data, 'cant find this book.');
        }

        return $this->success($data, 'found your book');
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $data = $request->validated();

        $book = Book::create($data);

        if ($book) {
            return $this->success($book, 'book created successfully.');
        }

        return $this->failure($book, 'cant create book.');
    }

    public function show(Book $book): JsonResponse
    {
        return $this->success($book);
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $book->update($request->validated());

        return $this->success($book, 'book updated successfully.');
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return $this->success($book, 'book deleted successfully.');
    }
}
