<?php

namespace App\Enums;

enum BasketballPosition: string
{
    case PointGuard = 'point_guard';
    case ShootingGuard = 'shooting_guard';
    case SmallForward = 'small_forward';
    case PowerForward = 'power_forward';
    case Center = 'center';

    public function label(): string
    {
        return match ($this) {
            self::PointGuard => 'Rozgrywający',
            self::ShootingGuard => 'Rzucający obrońca',
            self::SmallForward => 'Niski skrzydłowy',
            self::PowerForward => 'Silny skrzydłowy',
            self::Center => 'Środkowy',
        };
    }

    public function sortOrder(): int
    {
        return match ($this) {
            self::PointGuard => 1,
            self::ShootingGuard => 2,
            self::SmallForward => 3,
            self::PowerForward => 4,
            self::Center => 5,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $position): array => [$position->value => $position->label()])
            ->all();
    }
}
