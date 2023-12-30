<?php

namespace Cortexitsolution\ApiAuth\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "id"        => $this->id,
            "name"      => $this->name,
            "email"     => $this->email,           
            'phone'     => $this->phone,
            'address'   => $this->address,
            'city'      => $this->city,
            'state'     => $this->state,
            'postalCode' => $this->postal_code,
            'country'   => $this->country,
            'avatar'    => $this->avatar,
            "status"    => $this->status ? "active" : "in-active",
            'role'      => $this->role,
        ];
    }
}