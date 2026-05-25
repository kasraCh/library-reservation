<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class BookController extends ApiController
{
    public function index()
    {
        $books = Book::all();

        return $this->successResponse($books,'found all books');
    }

    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        $book = Book::create($data);

        if ($book) {
            return $this->successResponse(new BookResource($book), 'book created successfully');
        } else {
            return $this->errorResponse(null,'error',400);
        }
    }

    public function show(Book $book)
    {

        return response()->json([
            'data' => [
                'book' => $book
            ]
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return new BookResource($book)
            ->additional(['message' => 'Book updated successfully.']);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json([
            'data' => [
                'status' => 'success',
                'message' => 'book deleted'
            ]
        ]);
    }

}
