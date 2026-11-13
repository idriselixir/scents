<?php

namespace App\Livewire\Core\Shop;

use App\Models\Fragrance;
use App\Services\BagService;
use App\Services\FragranceCatalogService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('After Note — Arabic Fragrance & Scent Discovery')]
class Storefront extends Component
{
    public const string ALL_FAMILIES = 'all';

    public const int COMPARISON_LIMIT = 3;

    public string $family = self::ALL_FAMILIES;

    public string $search = '';

    /** @var array<int, string> */
    public array $comparedUuids = [];

    public ?string $mood = null;

    public ?string $viewedFragranceUuid = null;

    public string $addedToBagFeedback = '';

    public string $bagAnnouncement = '';

    private BagService $bag;

    private FragranceCatalogService $catalog;

    public function boot(BagService $bag, FragranceCatalogService $catalog): void
    {
        $this->bag = $bag;
        $this->catalog = $catalog;
    }

    public function selectFamily(string $familySlug): void
    {
        $this->family = $this->catalog->scentFamilies()->contains('slug', $familySlug)
            ? $familySlug
            : self::ALL_FAMILIES;
    }

    /**
     * The scent index under the hero starts a fresh browse, so it drops the search too.
     */
    public function browseFamily(string $familySlug): void
    {
        $this->search = '';
        $this->selectFamily($familySlug);
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->family = self::ALL_FAMILIES;
        $this->dispatch('focus-search');
    }

    public function updatedComparedUuids(): void
    {
        $this->comparedUuids = array_slice(array_values(array_unique($this->comparedUuids)), 0, self::COMPARISON_LIMIT);
    }

    public function clearComparison(): void
    {
        $this->comparedUuids = [];
        $this->dispatch('close-dialog', id: 'compare-dialog');
        $this->dispatch('focus-search');
    }

    public function selectMood(string $familySlug): void
    {
        $this->mood = $this->catalog->scentFamilies()->contains('slug', $familySlug) ? $familySlug : null;
    }

    public function showFragrance(string $uuid): void
    {
        if ($this->catalog->findListed($uuid) === null) {
            return;
        }

        $this->viewedFragranceUuid = $uuid;
        $this->addedToBagFeedback = '';
        $this->dispatch('open-dialog', id: 'product-dialog');
    }

    public function addViewedFragranceToBag(): void
    {
        $fragrance = $this->catalog->findListed($this->viewedFragranceUuid);

        if ($fragrance === null || $this->bag->hasReachedLimit($fragrance)) {
            return;
        }

        $quantity = $this->changeQuantity($fragrance, 1);
        $this->addedToBagFeedback = __('Added to bag · :quantity in your bag', ['quantity' => $quantity]);
    }

    public function changeBagQuantity(string $uuid, int $change): void
    {
        $fragrance = $this->catalog->findListed($uuid);

        if ($fragrance === null || ! in_array($change, [-1, 1], true)) {
            return;
        }

        if ($this->changeQuantity($fragrance, $change) === 0) {
            // The row and its buttons are gone, so the page moves focus to what is left.
            $this->dispatch('bag-row-removed');
        }
    }

    public function render(): View
    {
        $families = $this->catalog->scentFamilies();
        $listedFragrances = $this->catalog->listedFragrances();
        $bagLines = $this->bag->lines();

        return view('livewire.core.shop.storefront', [
            'families' => $families,
            'allFamilies' => self::ALL_FAMILIES,
            'comparisonLimit' => self::COMPARISON_LIMIT,
            'fragrances' => $this->catalog->search(
                familySlug: $this->family === self::ALL_FAMILIES ? null : $this->family,
                search: $this->search,
            ),
            'comparedFragrances' => $listedFragrances->whereIn('uuid', $this->comparedUuids)->values(),
            'finderMatches' => $this->mood === null ? collect() : $this->catalog->finderMatches($this->mood),
            'viewedFragrance' => $listedFragrances->firstWhere('uuid', $this->viewedFragranceUuid),
            'bagLines' => $bagLines,
            'bagItemCount' => $bagLines->sum('quantity'),
            'bagSubtotal' => $bagLines->sum('total'),
            'bag' => $this->bag,
        ]);
    }

    private function changeQuantity(Fragrance $fragrance, int $change): int
    {
        $quantity = $this->bag->changeQuantity($fragrance, $change);

        $this->bagAnnouncement = $quantity > 0
            ? __(':quantity × :name in your bag.', ['quantity' => $quantity, 'name' => $fragrance->name])
            : __(':name removed from your bag.', ['name' => $fragrance->name]);

        return $quantity;
    }
}
