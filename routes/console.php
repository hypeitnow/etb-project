<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\LzkoszLeagueTableService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('lzkosz:sync-table', function (LzkoszLeagueTableService $leagueTableService): int {
    $count = $leagueTableService->sync();
    $this->info("Tabela ŁZKosz została pobrana. Zaktualizowano {$count} drużyn.");

    return self::SUCCESS;
})->purpose('Sync the 3 Liga Mężczyzn table from ŁZKosz');
