<?php

use App\Enums\FragranceStatus;
use App\Models\Fragrance;
use App\Services\BagService;

it('adds, caps and removes quantities', function () {
    $bag = app(BagService::class);
    $fragrance = Fragrance::factory()->create(['price' => 4500]);

    expect($bag->changeQuantity($fragrance, 2))->toBe(2)
        ->and($bag->changeQuantity($fragrance, 500))->toBe(BagService::MAXIMUM_QUANTITY_PER_FRAGRANCE)
        ->and($bag->hasReachedLimit($fragrance))->toBeTrue()
        ->and($bag->changeQuantity($fragrance, -500))->toBe(0)
        ->and($bag->lines())->toBeEmpty();
});

it('lists rows in catalogue order with totals', function () {
    $bag = app(BagService::class);
    $second = Fragrance::factory()->create(['price' => 3000, 'position' => 2]);
    $first = Fragrance::factory()->create(['price' => 5200, 'position' => 1]);

    $bag->changeQuantity($second, 1);
    $bag->changeQuantity($first, 3);

    expect($bag->lines()->pluck('fragrance.id')->all())->toBe([$first->id, $second->id])
        ->and($bag->itemCount())->toBe(4)
        ->and($bag->subtotal())->toBe(18600.0);
});

it('drops fragrances that are no longer listed', function () {
    $bag = app(BagService::class);
    $fragrance = Fragrance::factory()->create();
    $bag->changeQuantity($fragrance, 1);

    $fragrance->update(['status' => FragranceStatus::HIDDEN]);

    expect($bag->itemCount())->toBe(0);
});

it('ignores tampered session values', function () {
    session(['bag' => ['not-a-uuid' => 'lots', Fragrance::factory()->create()->uuid => -4]]);

    expect(app(BagService::class)->lines())->toBeEmpty();
});
