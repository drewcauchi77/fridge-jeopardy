<?php

namespace App\Actions;

use App\Http\Requests\StoreImageRequest;
use App\Models\Image;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

final class CreateImageAction
{
    /**
     * @param array $attributes
     * @return Image
     */
    public function handle(array $attributes): Image
    {
        return DB::transaction(function () use ($attributes) {
            return Image::query()->create($attributes);
        });
    }

    /**
     * @param int $fridgeId
     * @param array<string> $imageUrls
     * @return Collection<int, Image>
     */
    public function createMultiple(int $fridgeId, array $imageUrls): Collection
    {
        $images = collect();
        $imageRequest = new StoreImageRequest;

        foreach ($imageUrls as $imageUrl) {
            $validator = Validator::make(['link' => $imageUrl], $imageRequest->rules());

            if ($validator->passes()) {
                $image = $this->handle([
                    'link' => $imageUrl,
                    'fridge_id' => $fridgeId,
                ]);

                $images->push($image);
            }
        }

        return $images;
    }
}
