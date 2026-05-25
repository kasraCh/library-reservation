<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all();

        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        $book = Book::create($data);

        if ($book) {
            return new BookResource($book)
                ->additional(['message' => 'Book created successfully.']);
        } else {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'cant create book'
                ]
            ], 400);
        }
    }

    public function show(Book $book)
    {

        $data = Book::where('id', $book)->firstOrFail();

        return response()->json([
            'data' => [
                'book' => $data
            ]
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        if (!$book) {
            return response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => 'book not found'
                ]
            ]);
        }

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
