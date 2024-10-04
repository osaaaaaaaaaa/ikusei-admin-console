<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
//            'level' => $this->level,
            'exp' => $this->exp,
            'created_at' => $this->created_at->format('Y/m/d H:i:s')
        ];
    }
}
