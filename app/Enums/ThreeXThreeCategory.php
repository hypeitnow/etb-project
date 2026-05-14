<?php

namespace App\Enums;

enum ThreeXThreeCategory: string
{
    case OpenM = 'open_m';
    case OpenK = 'open_k';
    case U11M = 'u11_m';
    case U11K = 'u11_k';
    case U12M = 'u12_m';
    case U12K = 'u12_k';
    case U13M = 'u13_m';
    case U13K = 'u13_k';
    case U14M = 'u14_m';
    case U14K = 'u14_k';
    case U15M = 'u15_m';
    case U15K = 'u15_k';
    case U16M = 'u16_m';
    case U16K = 'u16_k';
    case U17M = 'u17_m';
    case U17K = 'u17_k';
    case U18M = 'u18_m';
    case U18K = 'u18_k';
    case U19M = 'u19_m';
    case U19K = 'u19_k';
    case U23M = 'u23_m';
    case U23K = 'u23_k';

    public function label(): string
    {
        return match ($this) {
            self::OpenM => 'Open M',
            self::OpenK => 'Open K',
            self::U11M => 'U11 M',
            self::U11K => 'U11 K',
            self::U12M => 'U12 M',
            self::U12K => 'U12 K',
            self::U13M => 'U13 M',
            self::U13K => 'U13 K',
            self::U14M => 'U14 M',
            self::U14K => 'U14 K',
            self::U15M => 'U15 M',
            self::U15K => 'U15 K',
            self::U16M => 'U16 M',
            self::U16K => 'U16 K',
            self::U17M => 'U17 M',
            self::U17K => 'U17 K',
            self::U18M => 'U18 M',
            self::U18K => 'U18 K',
            self::U19M => 'U19 M',
            self::U19K => 'U19 K',
            self::U23M => 'U23 M',
            self::U23K => 'U23 K',
        };
    }

    public function group(): string
    {
        return str_starts_with($this->value, 'open') ? 'OPEN' : 'Kategorie młodzieżowe';
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        $options = [];

        foreach (self::cases() as $category) {
            $options[$category->group()][$category->value] = $category->label();
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $category): string => $category->value, self::cases());
    }
}
