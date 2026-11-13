<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\FragranceStatus;
use App\Enums\PackshotTone;
use Database\Factories\FragranceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'scent_family_id', 'slug', 'house', 'name', 'tone', 'size', 'concentration', 'price',
    'caption', 'description', 'opening_notes', 'heart_notes', 'dry_down_notes', 'source_url',
    'is_finder_pick', 'position', 'status',
])]
class Fragrance extends Model
{
    /** @use HasFactory<FragranceFactory> */
    use HasFactory;

    use HasUuid;

    public function scentFamily(): BelongsTo
    {
        return $this->belongsTo(ScentFamily::class);
    }

    /**
     * The notes in the order they are smelled, keyed by the stage's label.
     *
     * @return array<string, string>
     */
    public function notesByStage(): array
    {
        return [
            __('Opening') => $this->opening_notes,
            __('Heart') => $this->heart_notes,
            __('Dry-down') => $this->dry_down_notes,
        ];
    }

    public function photoUrl(): string
    {
        return asset("assets/{$this->slug}.webp");
    }

    public function formattedPrice(): string
    {
        return self::formatPrice($this->price);
    }

    public static function formatPrice(float|string $amount): string
    {
        return 'KSh '.number_format((float) $amount);
    }

    protected function casts(): array
    {
        return [
            'tone' => PackshotTone::class,
            'status' => FragranceStatus::class,
            'price' => 'decimal:2',
            'is_finder_pick' => 'boolean',
            'position' => 'integer',
        ];
    }
}
