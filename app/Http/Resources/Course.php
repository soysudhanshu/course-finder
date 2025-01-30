<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class Course extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'difficulty' => $this->difficulty->name(),
            'categories' => $this->categories->map(fn($category) => ['id' => $category->id, 'name' => $category->name]),
            'format' => $this->format->name(),
            'duration' => $this->duration,
            'price' => Number::currency($this->price, 'GBP'),
            'is_free' => $this->price == 0,
            'rating' => $this->rating,
            'instructor' => $this->instructor,
            'popularity' => $this->popularity->label(),

        ];
    }
}
