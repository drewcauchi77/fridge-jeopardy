<?php

namespace App\Actions;

use App\Models\Fridge;
use Illuminate\Support\Facades\DB;

final class CreateFridgeAction
{
    /**
     * @param array<mixed> $attributes
     * @return Fridge
     */
    public function handle(array $attributes): Fridge
    {
        return DB::transaction(function () use ($attributes) {
            return Fridge::query()->create($attributes);
        });
    }
}
