<?php

use App\Livewire\Core\Shop\Storefront;
use App\Models\Fragrance;
use App\Models\ScentFamily;
use App\Services\BagService;
use Database\Seeders\FragranceCatalogSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(FragranceCatalogSeeder::class);
});

function fragrance(string $slug): Fragrance
{
    return Fragrance::where('slug', $slug)->firstOrFail();
}

function shownFragranceNames($component): array
{
    return $component->viewData('fragrances')->pluck('name')->all();
}

it('serves the storefront with the whole collection', function () {
    $this->get('/')
        ->assertOk()
        ->assertSeeLivewire(Storefront::class)
        ->assertSee('12 fragrances')
        ->assertSee('Khamrah Qahwa')
        ->assertSee('KSh 4,900')
        ->assertSee('Compare (0/3)');
});

it('can be seeded twice without duplicating the catalogue', function () {
    $this->seed(FragranceCatalogSeeder::class);

    expect(Fragrance::count())->toBe(12)->and(ScentFamily::count())->toBe(4);
});

it('filters the collection by scent family', function () {
    $component = Livewire::test(Storefront::class)->call('selectFamily', 'fresh');

    expect(shownFragranceNames($component))->toBe(['Turathi Blue', '9 AM Dive', 'Qaed Al Fursan']);
    $component->assertSee('3 fragrances');
});

it('falls back to every family when the family is unknown', function () {
    Livewire::test(Storefront::class)
        ->call('selectFamily', 'nonsense')
        ->assertSet('family', Storefront::ALL_FAMILIES)
        ->assertSee('12 fragrances');
});

it('searches by bottle, house, note and family, matching every word', function (string $search, array $expectedNames) {
    $component = Livewire::test(Storefront::class)->set('search', $search);

    expect(shownFragranceNames($component))->toBe($expectedNames);
})->with([
    'a note' => ['leather', ['Rare Carbon']],
    'a house and a note' => ['  AFNAN  cedar ', ['9 AM Dive', 'Rare Carbon']],
    'a family label' => ['creamy', ['Yara', 'Mystique Bouquet']],
    'a wildcard character' => ['%', []],
]);

it('keeps the search when a filter is chosen but drops it from the scent index', function () {
    $component = Livewire::test(Storefront::class)->set('search', 'vanilla')->call('selectFamily', 'dark');
    expect(shownFragranceNames($component))->toBe(['9 PM', 'Asad']);

    $component->call('browseFamily', 'dark')->assertSet('search', '');
    expect(shownFragranceNames($component))->toBe(['9 PM', 'Asad', 'Rare Carbon', 'Oud for Glory']);
});

it('counts a single match in the singular', function () {
    Livewire::test(Storefront::class)->set('search', 'leather')->assertSeeHtml('>1 fragrance<');
});

it('shows an empty state with zero results and can clear the filters', function () {
    Livewire::test(Storefront::class)
        ->call('selectFamily', 'soft')
        ->set('search', 'bubblegum')
        ->assertSee('0 fragrances')
        ->assertSee('No fragrances match.')
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('family', Storefront::ALL_FAMILIES)
        ->assertDispatched('focus-search')
        ->assertSee('12 fragrances');
});

it('hides fragrances that are not listed', function () {
    Fragrance::factory()->hidden()->create(['name' => 'Unreleased Oud']);

    Livewire::test(Storefront::class)->assertDontSee('Unreleased Oud')->assertSee('12 fragrances');
});

it('compares at most three fragrances, in catalogue order', function () {
    $uuids = collect(['glory', 'yara', '9pm', 'asad'])->map(fn (string $slug) => fragrance($slug)->uuid)->all();

    $component = Livewire::test(Storefront::class)
        ->set('comparedUuids', array_slice($uuids, 0, 1))
        ->assertSee('Compare (1/3)')
        ->set('comparedUuids', $uuids)
        ->assertCount('comparedUuids', 3)
        ->assertSee('Compare (3/3)');

    expect($component->viewData('comparedFragrances')->pluck('name')->all())->toBe(['9 PM', 'Yara', 'Oud for Glory']);

    $component->call('clearComparison')
        ->assertSet('comparedUuids', [])
        ->assertDispatched('close-dialog', id: 'compare-dialog')
        ->assertSee('Compare (0/3)');
});

it('suggests three bottles for a mood, the family pick first', function () {
    $component = Livewire::test(Storefront::class)->call('selectMood', 'dark');

    expect($component->viewData('finderMatches')->pluck('name')->all())->toBe(['9 PM', 'Asad', 'Rare Carbon']);
});

it('opens a fragrance with its notes and source', function () {
    Livewire::test(Storefront::class)
        ->call('showFragrance', fragrance('khamrah')->uuid)
        ->assertDispatched('open-dialog', id: 'product-dialog')
        ->assertSee('Lattafa · Sweet & Warm')
        ->assertSee('Dates, praline, tuberose, lily of the valley')
        ->assertSee('https://lattafa.com/product/khamrah/')
        ->assertSee('Add to bag');
});

it('does not open a fragrance that is not listed', function () {
    $hidden = Fragrance::factory()->hidden()->create();

    Livewire::test(Storefront::class)
        ->call('showFragrance', $hidden->uuid)
        ->assertSet('viewedFragranceUuid', null)
        ->assertNotDispatched('open-dialog');
});

it('adds the viewed fragrance to the bag and totals it', function () {
    Livewire::test(Storefront::class)
        ->call('showFragrance', fragrance('9pm')->uuid)
        ->call('addViewedFragranceToBag')
        ->call('addViewedFragranceToBag')
        ->assertSet('addedToBagFeedback', 'Added to bag · 2 in your bag')
        ->assertSet('bagAnnouncement', '2 × 9 PM in your bag.')
        ->assertSee('Open bag, 2 items')
        ->call('showFragrance', fragrance('yara')->uuid)
        ->assertSet('addedToBagFeedback', '')
        ->call('addViewedFragranceToBag')
        ->assertSee('Open bag, 3 items')
        ->assertViewHas('bagSubtotal', 13700.0)
        ->assertSee('KSh 13,700');
});

it('changes quantities in the bag and removes a row at zero', function () {
    $uuid = fragrance('asad')->uuid;

    Livewire::test(Storefront::class)
        ->assertSee('Your bag is empty.')
        ->call('changeBagQuantity', $uuid, 1)
        ->call('changeBagQuantity', $uuid, 1)
        ->assertSee('Decrease quantity of Asad')
        ->call('changeBagQuantity', $uuid, -1)
        ->assertSee('Remove Asad')
        ->assertSee('Open bag, 1 item')
        ->assertNotDispatched('bag-row-removed')
        ->call('changeBagQuantity', $uuid, -1)
        ->assertDispatched('bag-row-removed')
        ->assertSet('bagAnnouncement', 'Asad removed from your bag.')
        ->assertSee('Your bag is empty.');
});

it('ignores quantity changes other than one step', function () {
    Livewire::test(Storefront::class)
        ->call('changeBagQuantity', fragrance('asad')->uuid, 50)
        ->assertSee('Your bag is empty.');
});

it('stops adding at the bag limit', function () {
    $nebras = fragrance('nebras');
    app(BagService::class)->changeQuantity($nebras, BagService::MAXIMUM_QUANTITY_PER_FRAGRANCE);

    Livewire::test(Storefront::class)
        ->call('showFragrance', $nebras->uuid)
        ->assertSee('Bag limit reached')
        ->call('addViewedFragranceToBag')
        ->assertSet('addedToBagFeedback', '');

    expect(app(BagService::class)->quantityOf($nebras))->toBe(99);
});
