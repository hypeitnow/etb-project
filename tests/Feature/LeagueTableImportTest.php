<?php

use App\Models\LeagueStanding;
use App\Models\Opponent;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('imports the lzkosz league table into local opponents and standings', function () {
    Http::fake([
        'lzkosz.pl/liga/215/tabela.html' => Http::response(<<<'HTML'
            <table>
                <thead>
                    <tr>
                        <th>m</th><th>drużyna</th><th>pkt</th><th>mecze</th><th>zw. - por.</th>
                        <th>dom</th><th>wyjazd</th><th>pkt. zd. - pkt. str.</th><th>różnica</th><th>stosunek</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td><td><a href="/liga/215/druzyny/d/13647/skk.html">ŚKK </a></td><td>41</td><td>22</td><td>19 - 3</td><td>11 - 0</td><td>8 - 3</td><td>2238 - 1541</td><td>+697</td><td>1.4523</td>
                    </tr>
                    <tr>
                        <td>9</td><td><a href="/liga/215/druzyny/d/13300/profi-sunbud-pkk-99-pabianice.html">PKK 99 </a></td><td>31</td><td>22</td><td>9 - 13</td><td>5 - 6</td><td>4 - 7</td><td>1686 - 1847</td><td>-161</td><td>0.9128</td>
                    </tr>
                </tbody>
            </table>
        HTML),
    ]);

    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $response = $this->actingAs($admin)->post(route('admin.league-table.sync'));

    $response->assertRedirect(route('profile.edit'));
    $this->assertDatabaseHas('opponents', [
        'name' => 'PKK 99',
        'source_team_url' => 'https://lzkosz.pl/liga/215/druzyny/d/13300/profi-sunbud-pkk-99-pabianice.html',
    ]);
    $this->assertDatabaseHas('league_standings', [
        'season' => '2025/2026',
        'league_id' => 215,
        'position' => 9,
        'points' => 31,
        'games' => 22,
        'wins' => 9,
        'losses' => 13,
        'points_for' => 1686,
        'points_against' => 1847,
        'points_difference' => -161,
    ]);

    expect(Opponent::query()->count())->toBe(2);
    expect(LeagueStanding::query()->count())->toBe(2);
});
