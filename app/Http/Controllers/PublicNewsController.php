<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;

class PublicNewsController extends Controller
{
    public function index(): View
    {
        $newsItems = News::query()
            ->with(['author', 'images'])
            ->active()
            ->published()
            ->latest('publish_at')
            ->latest()
            ->get();

        return view('pages.news', [
            'featuredNews' => $newsItems->take(5),
            'newsItems' => $newsItems,
        ]);
    }

    public function show(News $news): View
    {
        abort_unless($news->isPubliclyVisible(), 404);

        $news->load(['author', 'images']);

        return view('pages.news-show', compact('news'));
    }
}
