<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Traits\ApiResponse\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        //        $books = Cache::remember('books.list', 60, function () {
        //            return Book::query()->get()->toArray();
        //        });
        $payload = Cache::remember('books', 60, function () {
            $book = Book::all();

            return BookResource::collection($book)->resolve();
        });

        return $this->success($payload, 'Books retrieved successfully.');
    }

    public function find(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search'));

        if ($search === '') {
            $book = Book::all();

            return $this->success($book, 'Books retrieved successfully.');
        }

        $books = Book::query()
            ->findBook($search)
            ->latest()
            ->get();

        if ($books->isEmpty()) {
            return $this->failure(null, 'No books found.');
        }

        return $this->success(
            BookResource::collection($books),
            'Books found successfully'
        );
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $data = $request->validated();

        $book = Book::create($data);

        if ($book) {
            return $this->success(new BookResource($book), 'book created successfully.');
        }

        return $this->failure($book, 'cant create book.');
    }

    public function show(Book $book): JsonResponse
    {
        return $this->success(new BookResource($book), sprintf('Book no.%d retrieved successfully.', $book->id));
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $book->update($request->validated());

        return $this->success(new BookResource($book), sprintf('Book no.%d update successfully.', $book->id));
    }

    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return $this->success(null, 'Bookdelete successfully.');
    }
}
