<?php

namespace App\Http\Controllers;

use App\Models\ClubSection;
use App\Models\Sponsor;
use Illuminate\View\View;

class PublicClubController extends Controller
{
    public function index(): View
    {
        return view('pages.club', [
            'clubSections' => $this->sections(),
            'clubSponsorsByType' => $this->sponsorsByType(),
            'clubSponsorTypes' => Sponsor::types(),
        ]);
    }

    public function show(string $section): View
    {
        $clubSection = $this->sections()->firstWhere('slug', $section);

        abort_unless($clubSection, 404);

        return view('pages.club-section', [
            'clubSection' => $clubSection,
            'clubSponsorsByType' => $this->sponsorsByType(),
            'clubSponsorTypes' => Sponsor::types(),
        ]);
    }

    public function contact(): View
    {
        $clubSection = $this->sections()->firstWhere('slug', 'contact');

        abort_unless($clubSection, 404);

        return view('pages.contact', [
            'clubSection' => $clubSection,
        ]);
    }

    private function sections()
    {
        ClubSection::syncDefaults();

        return ClubSection::query()
            ->with('images')
            ->whereIn('slug', array_keys(ClubSection::SECTIONS))
            ->orderBy('sort_order')
            ->get();
    }

    private function sponsorsByType()
    {
        return Sponsor::query()
            ->active()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');
    }
}
