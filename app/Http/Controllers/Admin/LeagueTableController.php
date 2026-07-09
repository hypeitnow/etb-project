<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Opponent;
use App\Services\LzkoszLeagueTableService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class LeagueTableController extends Controller
{
    public function sync(LzkoszLeagueTableService $leagueTableService): RedirectResponse
    {
        try {
            $count = $leagueTableService->sync();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('profile.edit')
                ->with('error', 'Nie udało się pobrać tabeli ŁZKosz. Spróbuj ponownie za chwilę.');
        }

        return redirect()
            ->route('profile.edit')
            ->with('success', "Tabela ŁZKosz została pobrana. Zaktualizowano {$count} drużyn.");
    }

    public function updateOpponent(Request $request, Opponent $opponent): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($opponent->logo_path) {
                Storage::disk('public')->delete($opponent->logo_path);
            }

            $opponent->logo_path = $request->file('logo')->store('team-logos', 'public');
            $opponent->save();

            if (Str::of($opponent->name)->lower()->contains('etb')) {
                AppSetting::setValue('default_home_logo', $opponent->logo_path);
            }
        }

        return back()->with('success', 'Logo drużyny zostało zaktualizowane.');
    }
}
