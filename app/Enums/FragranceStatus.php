<?php

namespace App\Enums;

enum FragranceStatus: int
{
    case LISTED = 1;
    case HIDDEN = 2;

    public function label(): string
    {
        return match ($this) {
            self::LISTED => __('Listed'),
            self::HIDDEN => __('Hidden'),
        };
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status): array => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }
}
