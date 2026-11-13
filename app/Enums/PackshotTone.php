<?php

namespace App\Enums;

enum PackshotTone: int
{
    case SMOKE = 1;
    case HONEY = 2;
    case MINERAL = 3;
    case PETAL = 4;

    public function label(): string
    {
        return match ($this) {
            self::SMOKE => __('Smoke'),
            self::HONEY => __('Honey'),
            self::MINERAL => __('Mineral'),
            self::PETAL => __('Petal'),
        };
    }

    /**
     * The class in shop.css that paints the backdrop behind a bottle photo.
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::SMOKE => 'smoke',
            self::HONEY => 'honey',
            self::MINERAL => 'mineral',
            self::PETAL => 'petal',
        };
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $tone): array => ['value' => $tone->value, 'label' => $tone->label()],
            self::cases(),
        );
    }
}
