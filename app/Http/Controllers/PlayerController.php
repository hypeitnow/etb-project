<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Requests\UpdatePlayerRequest;
use App\Models\Player;
use App\Services\AdminNotificationService;
use App\Services\PlayerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlayerController extends Controller
{
    public function __construct(
        private readonly PlayerService $playerService,
        private readonly AdminNotificationService $notificationService
    )
    {
    }

    public function index(): View
    {
        $this->authorize('viewAny', Player::class);

        $players = Player::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('players.index', compact('players'));
    }

    public function create(): View
    {
        $this->authorize('create', Player::class);

        return view('players.create');
    }

    public function store(StorePlayerRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $player = $this->playerService->create($data, $request->file('photo'));
        $this->notificationService->record($request->user(), 'created', $player, "Zawodnik: {$player->full_name}");

        return redirect()->route('profile.edit')->with('success', 'Zawodnik został zapisany.');
    }

    public function show(Player $player): View
    {
        $this->authorize('view', $player);

        return view('players.show', compact('player'));
    }

    public function edit(Player $player): View
    {
        $this->authorize('update', $player);

        return view('players.edit', compact('player'));
    }

    public function update(UpdatePlayerRequest $request, Player $player): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $this->playerService->update($player, $data, $request->file('photo'));
        $this->notificationService->record($request->user(), 'updated', $player, "Zawodnik: {$player->full_name}");

        return redirect()->route('profile.edit')->with('success', 'Zawodnik został zaktualizowany.');
    }

    public function destroy(Player $player): RedirectResponse
    {
        $this->authorize('delete', $player);

        $label = "Zawodnik: {$player->full_name}";
        $id = $player->id;
        $this->playerService->delete($player);
        $this->notificationService->recordDeleted(request()->user(), Player::class, $id, $label);

        return back()->with('success', 'Zawodnik został usunięty.');
    }
}
