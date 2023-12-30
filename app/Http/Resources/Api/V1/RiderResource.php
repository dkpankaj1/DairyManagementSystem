<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            "name" => $this->user->name,
            "email" => $this->user->email,
            'phone' => $this->user->phone,
            'address' => $this->user->address,
            'city' => $this->user->city,
            'state' => $this->user->state,
            'postalCode' => $this->user->postal_code,
            'country' => $this->user->country,
            'avatar' => $this->user->avatar,
            'status' => $this->user->status
        ];
    }
}
