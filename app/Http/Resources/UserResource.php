<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'username' => $this->username,
            'phoneNumber' => $this->phone_number,
            'name' => $this->name,
            'isAdmin' => (bool)$this->is_admin,
            'cart' => $this->whenLoaded('cart')
        ];
    }
}
