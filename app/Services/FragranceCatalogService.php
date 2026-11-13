<?php

namespace App\Services;

use App\Enums\FragranceStatus;
use App\Models\Fragrance;
use App\Models\ScentFamily;
use Illuminate\Database\Eloquent\Collection;

class FragranceCatalogService
{
    public const int FINDER_MATCH_LIMIT = 3;

    /**
     * @return Collection<int, ScentFamily>
     */
    public function scentFamilies(): Collection
    {
        return ScentFamily::query()->orderBy('position')->get();
    }

    /**
     * @return Collection<int, Fragrance>
     */
    public function listedFragrances(): Collection
    {
        return Fragrance::query()
            ->with('scentFamily')
            ->where('status', FragranceStatus::LISTED)
            ->orderBy('position')
            ->get();
    }

    /**
     * Listed fragrances in a family (null for every family) where every word of the
     * search appears in the bottle's name, house, caption, family or notes.
     *
     * The catalogue is a few dozen bottles, so matching runs in PHP: it behaves the
     * same on sqlite and Postgres and treats % and _ as ordinary characters.
     *
     * @return Collection<int, Fragrance>
     */
    public function search(?string $familySlug = null, string $search = ''): Collection
    {
        $words = preg_split('/\s+/', mb_strtolower(trim($search)), flags: PREG_SPLIT_NO_EMPTY);

        return $this->listedFragrances()
            ->when($familySlug, fn (Collection $fragrances) => $fragrances->where('scentFamily.slug', $familySlug))
            ->filter(function (Fragrance $fragrance) use ($words): bool {
                $searchableText = mb_strtolower(implode(' ', [
                    $fragrance->name,
                    $fragrance->house,
                    $fragrance->caption,
                    $fragrance->scentFamily->label,
                    ...array_values($fragrance->notesByStage()),
                ]));

                return array_all($words, fn (string $word): bool => str_contains($searchableText, $word));
            })
            ->values();
    }

    /**
     * The bottles the scent finder suggests for a family, its pick first.
     *
     * @return Collection<int, Fragrance>
     */
    public function finderMatches(string $familySlug): Collection
    {
        return $this->search($familySlug)
            ->sortByDesc('is_finder_pick')
            ->take(self::FINDER_MATCH_LIMIT)
            ->values();
    }

    public function findListed(?string $uuid): ?Fragrance
    {
        return $uuid === null ? null : $this->listedFragrances()->firstWhere('uuid', $uuid);
    }
}
