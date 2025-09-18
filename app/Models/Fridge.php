<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $author
 * @property string $permalink
 * @property float $post_created_at
 * @property-read Collection<int, Image> $images
 */
class Fridge extends Model
{
    /**
     * @var array<string, string>
     */
    protected $casts = [
        'post_created_at' => 'decimal:1',
    ];

    /**
     * @return HasMany<Image, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }
}
