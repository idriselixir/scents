<?php

namespace App\Models;

use App\Concerns\HasUuid;
use Database\Factories\ScentFamilyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['slug', 'label', 'mood', 'example_notes', 'position'])]
class ScentFamily extends Model
{
    /** @use HasFactory<ScentFamilyFactory> */
    use HasFactory;

    use HasUuid;

    public function fragrances(): HasMany
    {
        return $this->hasMany(Fragrance::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
