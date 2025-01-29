<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Serie extends Model
{
    protected $attributes = [
        'logo_path' => '',
    ];

    protected $fillable = [
        'tcgdex_id',
        'name',
        'logo_path',
    ];

    /**
     * @return HasMany<Set, $this>
     */
    public function set(): HasMany
    {
        return $this->hasMany(Set::class);
    }
}
