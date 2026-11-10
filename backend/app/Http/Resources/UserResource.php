<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * Kata sandi tidak pernah ikut diserialisasi (juga diblokir $hidden di model User).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'dibuat_pada' => $this->created_at?->format('Y-m-d H:i:s'),
            'diubah_pada' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
