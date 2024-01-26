<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'rating' => $this->rating,
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'category' => isset($this->category) ? $this->category->name : null,
            'color' => isset($this->color) ? $this->color->name : null,
            'discounts' => DiscountResource::collection($this->whenLoaded('discounts')),

            // If coming from cart
            'count' => $this->whenPivotLoaded('cart_product', function () {
                return $this->pivot->count;
            }),
        ];
    }
}
