<?php

namespace App\Http\Controllers;

use App\Actions\CreateFridgeAction;
use App\Http\Requests\StoreFridgeRequest;
use App\Models\Fridge;

class FridgeController extends Controller
{
    public function store(
        StoreFridgeRequest $request,
        CreateFridgeAction $createFridge
    ): Fridge
    {
        $validated = $request->validated();

        $attributes = [
            'author' => $validated['author'],
            'permalink' => $validated['permalink'],
            'post_created_at' => $validated['post_created_at'],
        ];

        return $createFridge->handle($attributes);
    }
}
