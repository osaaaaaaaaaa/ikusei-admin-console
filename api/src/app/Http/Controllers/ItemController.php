<?php

namespace App\Http\Controllers;

use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::All();
        return response()->json(ItemResource::collection($items));
    }
}
