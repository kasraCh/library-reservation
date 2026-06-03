<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    private int $user_id;
    private int $book_id;
    private string $status;
    private mixed $due_date;
    private mixed $returned_at;
    private mixed $reserved_at;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //        return parent::toArray($request);

        return [
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'status' => $this->status,
            'reserved_at' => $this->reserved_at,
            'due_date' => $this->due_date,
            'returned_at' => $this->returned_at,
        ];
    }
}
