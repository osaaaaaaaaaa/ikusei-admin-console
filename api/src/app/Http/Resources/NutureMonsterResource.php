<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NutureMonsterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "monster_id" => $this->monster_id,
            "parent1_id" => $this->parent1_id,
            "parent2_id" => $this->parent2_id,
            "name" => $this->name,
            "level" => $this->level,
            "exp" => $this->exp,
            "stomach_vol" => $this->stomach_vol,
            "state" => $this->state,
            "created_at" => $this->created_at
        ];
    }
}
