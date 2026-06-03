<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{

    private string $title;
    private string $author;
    private string $isbn;
    private int $total_copies;
    private int $available_copies;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
        ];
    }
}
