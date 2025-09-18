<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $link
 * @property int $fridge_id
 * @property-read Fridge $fridge
 */
class Image extends Model
{
    /**
     * @return BelongsTo<Fridge, $this>
     */
    public function fridge(): BelongsTo
    {
        return $this->belongsTo(Fridge::class);
    }
}
