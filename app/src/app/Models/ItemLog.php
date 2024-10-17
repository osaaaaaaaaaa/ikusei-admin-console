<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemLog extends Model
{
    protected function casts(): array
    {
        return [
            'action_flag' => 'boolean',
        ];
    }
}
