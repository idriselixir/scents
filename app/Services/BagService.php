<?php

namespace App\Services;

use App\Models\Fragrance;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

class BagService
{
    public const int MAXIMUM_QUANTITY_PER_FRAGRANCE = 99;

    private const string SESSION_KEY = 'bag';

    public function __construct(
        private readonly Session $session,
        private readonly FragranceCatalogService $catalog,
    ) {}

    public function quantityOf(Fragrance $fragrance): int
    {
        return $this->quantities()[$fragrance->uuid] ?? 0;
    }

    public function hasReachedLimit(Fragrance $fragrance): bool
    {
        return $this->quantityOf($fragrance) >= self::MAXIMUM_QUANTITY_PER_FRAGRANCE;
    }

    /**
     * Adds to (or, with a negative change, takes from) a fragrance's quantity and
     * returns the new quantity. Dropping to zero removes it from the bag.
     */
    public function changeQuantity(Fragrance $fragrance, int $change): int
    {
        $quantities = $this->quantities();
        $quantity = max(0, min(self::MAXIMUM_QUANTITY_PER_FRAGRANCE, ($quantities[$fragrance->uuid] ?? 0) + $change));

        if ($quantity > 0) {
            $quantities[$fragrance->uuid] = $quantity;
        } else {
            unset($quantities[$fragrance->uuid]);
        }

        $this->session->put(self::SESSION_KEY, $quantities);

        return $quantity;
    }

    /**
     * The bag's rows in catalogue order. Fragrances that are no longer listed drop out.
     *
     * @return Collection<int, array{fragrance: Fragrance, quantity: int, total: float}>
     */
    public function lines(): Collection
    {
        $quantities = $this->quantities();

        return $this->catalog->listedFragrances()
            ->filter(fn (Fragrance $fragrance): bool => isset($quantities[$fragrance->uuid]))
            ->map(fn (Fragrance $fragrance): array => [
                'fragrance' => $fragrance,
                'quantity' => $quantities[$fragrance->uuid],
                'total' => (float) $fragrance->price * $quantities[$fragrance->uuid],
            ])
            ->values()
            ->toBase();
    }

    public function itemCount(): int
    {
        return $this->lines()->sum('quantity');
    }

    public function subtotal(): float
    {
        return $this->lines()->sum('total');
    }

    /**
     * @return array<string, int>
     */
    private function quantities(): array
    {
        return array_filter(
            (array) $this->session->get(self::SESSION_KEY, []),
            fn (mixed $quantity): bool => is_int($quantity) && $quantity > 0,
        );
    }
}
