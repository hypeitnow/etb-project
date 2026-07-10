<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class PolishHolidayService
{
    private const POLISH_PUBLIC_HOLIDAY_NAMES = [
        '01-01' => 'Nowy Rok',
        '01-06' => 'Święto Trzech Króli',
        '05-01' => 'Święto Pracy',
        '05-03' => 'Święto Konstytucji 3 Maja',
        '08-15' => 'Wniebowzięcie Najświętszej Maryi Panny',
        '11-01' => 'Wszystkich Świętych',
        '11-11' => 'Narodowe Święto Niepodległości',
        '12-24' => 'Wigilia Bożego Narodzenia',
        '12-25' => 'Boże Narodzenie',
        '12-26' => 'Drugi Dzień Świąt Bożego Narodzenia',
    ];

    private const MOVABLE_POLISH_PUBLIC_HOLIDAY_NAMES = [
        'Easter Sunday' => 'Wielkanoc',
        'Easter Monday' => 'Drugi Dzień Wielkanocy',
        'Pentecost' => 'Zielone Świątki',
        'Corpus Christi' => 'Boże Ciało',
    ];

    public function between(CarbonInterface $start, CarbonInterface $end): Collection
    {
        if (! config('services.nager_date.enabled', true)) {
            return collect();
        }

        $years = range((int) $start->format('Y'), (int) $end->format('Y'));

        return collect($years)
            ->flatMap(fn (int $year) => $this->forYear($year))
            ->filter(fn (array $holiday) => $holiday['date'] >= $start->toDateString() && $holiday['date'] <= $end->toDateString())
            ->values();
    }

    private function forYear(int $year): Collection
    {
        return collect(Cache::remember(
            "polish_public_holidays:v2:{$year}",
            now()->addDay(),
            fn () => $this->fetchYear($year),
        ))->map(fn (array $holiday) => [
            'date' => $holiday['date'],
            'name' => $this->polishName($holiday),
        ]);
    }

    private function fetchYear(int $year): array
    {
        $baseUrl = rtrim((string) config('services.nager_date.base_url', 'https://date.nager.at'), '/');

        try {
            $response = Http::timeout(5)
                ->acceptJson()
                ->get("{$baseUrl}/api/v4/Holidays/PL/{$year}");

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json())
                ->filter(fn (array $holiday) => ($holiday['nationalHoliday'] ?? false)
                    && in_array('Public', $holiday['holidayTypes'] ?? [], true))
                ->map(fn (array $holiday) => [
                    'date' => $holiday['date'],
                    'name' => $this->polishName($holiday),
                ])
                ->values()
                ->all();
        } catch (Throwable) {
            return [];
        }
    }

    private function polishName(array $holiday): string
    {
        $dateKey = substr((string) $holiday['date'], 5);
        $apiName = (string) $holiday['name'];

        return self::POLISH_PUBLIC_HOLIDAY_NAMES[$dateKey]
            ?? self::MOVABLE_POLISH_PUBLIC_HOLIDAY_NAMES[$apiName]
            ?? $apiName;
    }
}
