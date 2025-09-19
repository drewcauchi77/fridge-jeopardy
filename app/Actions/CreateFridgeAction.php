<?php

namespace App\Actions;

use App\Models\Fridge;
use Illuminate\Support\Facades\DB;

final readonly class CreateFridgeAction
{
    public function __construct(
        private CreateImageAction $createImageAction
    ) {}

    /**
     * @param  array<mixed>  $attributes
     * @param  array<string>  $imageUrls
     */
    public function handle(array $attributes, array $imageUrls = []): Fridge
    {
        return DB::transaction(function () use ($attributes, $imageUrls) {
            $fridge = Fridge::query()->create($attributes);

            if (!empty($imageUrls)) {
                $this->createImageAction->createMultiple($fridge->id, $imageUrls);
            }

            return $fridge->load('images');
        });
    }
}
