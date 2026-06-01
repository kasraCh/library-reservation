<?php

namespace App\Http\Controllers\Books;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BookController extends ApiController
{
    public function index()
    {
//        DB::listen(fn ($query) => info($query->toRawSql()));

//        $books = Book::query()->with('reservations')->get();

        $books = Cache::remember('books.list', 60, function () {
            return Book::query()->get()->toArray();
        });

        return $this->successResponse($books,'found all books');
    }

    public function find()
    {
        $data = Book::filter(request()->all())->orderBy('created_at', 'desc')->get();

        if (!empty($data)) {
            return $this->successResponse($data,'found books');
        }
        return $this->successResponse($data,'found books');
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
        return $this->successResponse($book,'found book');
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return $this->successResponse($book,'book updated successfully');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return $this->successResponse($book,'book deleted successfully');
    }

}
