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
            'food_vol' => $this->food_vol,
            'facility_lv' => $this->facility_lv,
            'reroll_num' => $this->reroll_num
        ];
    }
}
