<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct(private readonly NewsService $newsService)
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', News::class);

        $newsItems = News::query()
            ->with(['author', 'images'])
            ->where(function ($query): void {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->latest()
            ->get();

        return view('news.index', compact('newsItems'));
    }

    public function show(News $news): View
    {
        $this->authorize('view', $news);

        $news->load(['author', 'images']);

        return view('news.show', compact('news'));
    }

    public function create(): View
    {
        $this->authorize('create', News::class);

        return view('news.create');
    }

    public function store(StoreNewsRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['main_image', 'gallery']);
        $gallery = $request->file('gallery', []);

        $this->newsService->create($data, $request->user()->id, $request->file('main_image'), $gallery);

        return redirect()->route('profile.edit')->with('success', 'Aktualność została zapisana.');
    }

    public function edit(News $news): View
    {
        $this->authorize('update', $news);

        return view('news.edit', compact('news'));
    }

    public function update(UpdateNewsRequest $request, News $news): RedirectResponse
    {
        $data = $request->safe()->except(['main_image', 'gallery']);
        $gallery = $request->file('gallery', []);

        $this->newsService->update($news, $data, $request->file('main_image'), $gallery);

        return redirect()->route('profile.edit')->with('success', 'Aktualność została zaktualizowana.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $this->authorize('delete', $news);

        $this->newsService->delete($news);

        return back()->with('success', 'Aktualność została usunięta.');
    }
}
