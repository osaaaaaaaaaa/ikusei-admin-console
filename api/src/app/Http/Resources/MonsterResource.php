<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonsterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text,
            'evo_lv' => $this->evo_lv,
            'evo_id' => $this->evo_id,
            'rarity' => $this->rarity
        ];
    }
}
