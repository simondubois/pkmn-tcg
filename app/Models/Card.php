<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Rarity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Card extends Model
{
    protected $attributes = [
        'scan_path' => '',
    ];

    protected $fillable = [
        'set_id',
        'tcgdex_id',
        'name',
        'category',
        'rarity',
        'set_position',
        'scan_path',
    ];

    protected function casts(): array
    {
        return [
            'category' => Category::class,
            'rarity' => Rarity::class,
        ];
    }

    /**
     * @return BelongsTo<Set, $this>
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class);
    }
}
