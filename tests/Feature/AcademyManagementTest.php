<?php

use App\Models\AcademyGroup;
use App\Models\AcademyTraining;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('shows academy groups and trainings on the public academy page', function () {
    $group = AcademyGroup::query()->create([
        'name' => 'Juniorzy U17M',
        'code' => 'U17M',
        'color' => '#facc15',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $group->trainers()->create([
        'name' => 'Jan Trener',
        'role' => 'Trener główny',
        'email' => 'jan@example.com',
        'phone' => '500 100 200',
        'sort_order' => 1,
    ]);

    AcademyTraining::query()->create([
        'academy_group_id' => $group->id,
        'title' => 'Trening rzutowy',
        'starts_at' => now()->addDays(2)->setTime(18, 30),
        'ends_at' => now()->addDays(2)->setTime(20, 0),
        'location' => 'Hala ETB',
        'trainer_name' => 'Jan Trener',
        'status' => AcademyTraining::STATUS_SCHEDULED,
    ]);

    $response = $this->get(route('academy'));

    $response->assertOk();
    $response->assertSee('Juniorzy U17M');
    $response->assertSee('U17M');
    $response->assertSee('Trening rzutowy');
    $response->assertSee('18:30');
    $response->assertSee(route('academy.groups.show', $group));
});

it('uses Polish month names and Polish diacritics on academy pages', function () {
    $group = AcademyGroup::query()->create([
        'name' => 'Żacy U11',
        'code' => 'U11',
        'color' => '#facc15',
        'is_active' => true,
    ]);

    AcademyTraining::query()->create([
        'academy_group_id' => $group->id,
        'title' => 'Trening ogólny',
        'starts_at' => '2026-03-12 18:00:00',
        'ends_at' => '2026-03-12 19:30:00',
        'location' => 'Hala Łódź',
        'trainer_name' => 'Łukasz Trener',
        'status' => AcademyTraining::STATUS_CANCELLED,
        'cancelled_reason' => 'Zamknięta hala.',
    ]);

    $response = $this->get(route('academy', ['month' => '2026-03-01']));

    $response->assertOk();
    $response->assertSee('<html lang="pl">', false);
    $response->assertSee('marzec 2026');
    $response->assertSee('Szczegóły');
    $response->assertSee('Odwołany');
    $response->assertSee('Trening odwołany');
    $response->assertSee('bg-yellow-400');
    $response->assertDontSee('March 2026');
    $response->assertDontSee('Odwolany');
});

it('shows group details with trainers messages upcoming trainings and cancelled training reason', function () {
    $group = AcademyGroup::query()->create([
        'name' => 'Kadeci U15M',
        'code' => 'U15M',
        'color' => '#38bdf8',
        'is_active' => true,
    ]);

    $group->trainers()->create([
        'name' => 'Anna Coach',
        'role' => 'Trenerka',
        'email' => 'anna@example.com',
        'phone' => '600 200 300',
    ]);

    $group->messages()->create([
        'title' => 'Badania sportowe',
        'body' => 'Przyniescie aktualne badania na najblizszy trening.',
        'is_published' => true,
        'published_at' => now(),
    ]);

    AcademyTraining::query()->create([
        'academy_group_id' => $group->id,
        'title' => 'Trening techniczny',
        'starts_at' => now()->addDay()->setTime(17, 0),
        'ends_at' => now()->addDay()->setTime(18, 30),
        'location' => 'Sala SP 1',
        'trainer_name' => 'Anna Coach',
        'status' => AcademyTraining::STATUS_CANCELLED,
        'cancelled_reason' => 'Zamknieta hala.',
    ]);

    $response = $this->get(route('academy.groups.show', $group));

    $response->assertOk();
    $response->assertSee('Kadeci U15M');
    $response->assertSee('Anna Coach');
    $response->assertSee('anna@example.com');
    $response->assertSee('Badania sportowe');
    $response->assertSee('Przyniescie aktualne badania');
    $response->assertSee('Trening techniczny');
    $response->assertSee('Odwołany');
    $response->assertSee('Zamknieta hala.');
});

it('lets an admin manage academy groups trainers messages and cancelled trainings', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->post(route('admin.academy.groups.store'), [
        'name' => 'Mlodzicy U13',
        'code' => 'U13',
        'color' => '#22c55e',
        'description' => 'Grupa mlodzikow ETB.',
        'sort_order' => 3,
        'is_active' => '1',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $group = AcademyGroup::query()->where('code', 'U13')->firstOrFail();

    $this->actingAs($admin)->post(route('admin.academy.groups.trainers.store', $group), [
        'name' => 'Piotr Trener',
        'role' => 'Asystent',
        'email' => 'piotr@example.com',
        'phone' => '700 300 400',
        'sort_order' => 2,
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $this->actingAs($admin)->post(route('admin.academy.groups.messages.store', $group), [
        'title' => 'Sparing w sobote',
        'body' => 'Zbiorka o 9:30 przy hali.',
        'is_published' => '1',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $this->actingAs($admin)->post(route('admin.academy.trainings.store'), [
        'academy_group_id' => $group->id,
        'title' => 'Trening motoryczny',
        'starts_at' => now()->addDays(3)->setTime(16, 15)->format('Y-m-d\TH:i'),
        'ends_at' => now()->addDays(3)->setTime(17, 45)->format('Y-m-d\TH:i'),
        'location' => 'Hala ETB',
        'trainer_name' => 'Piotr Trener',
        'description' => 'Praca nad szybkoscia.',
        'status' => AcademyTraining::STATUS_SCHEDULED,
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $training = AcademyTraining::query()->where('title', 'Trening motoryczny')->firstOrFail();

    $this->actingAs($admin)->patch(route('admin.academy.trainings.cancel', $training), [
        'cancelled_reason' => 'Wyjazd trenera na turniej.',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $this->assertDatabaseHas('academy_trainers', ['academy_group_id' => $group->id, 'name' => 'Piotr Trener']);
    $this->assertDatabaseHas('academy_messages', ['academy_group_id' => $group->id, 'title' => 'Sparing w sobote']);
    $this->assertDatabaseHas('academy_trainings', [
        'id' => $training->id,
        'status' => AcademyTraining::STATUS_CANCELLED,
        'cancelled_reason' => 'Wyjazd trenera na turniej.',
    ]);
});

it('lets an admin filter academy trainings by date in the panel', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $group = AcademyGroup::query()->create([
        'name' => 'Juniorzy U17M',
        'code' => 'U17M',
        'color' => '#3b82f6',
        'is_active' => true,
    ]);

    AcademyTraining::query()->create([
        'academy_group_id' => $group->id,
        'title' => 'Trening wtorkowy',
        'starts_at' => '2026-03-10 18:00:00',
        'ends_at' => '2026-03-10 19:30:00',
        'location' => 'Hala ETB',
        'trainer_name' => 'Jan Trener',
        'status' => AcademyTraining::STATUS_SCHEDULED,
    ]);

    AcademyTraining::query()->create([
        'academy_group_id' => $group->id,
        'title' => 'Trening czwartkowy',
        'starts_at' => '2026-03-12 18:00:00',
        'ends_at' => '2026-03-12 19:30:00',
        'location' => 'Hala ETB',
        'trainer_name' => 'Jan Trener',
        'status' => AcademyTraining::STATUS_SCHEDULED,
    ]);

    $response = $this->actingAs($admin)->get(route('profile.edit', [
        'section' => 'academy',
        'academy_training_date' => '2026-03-10',
    ]));

    $response->assertOk();
    $response->assertSee('Trening wtorkowy');
    $response->assertDontSee('Trening czwartkowy');
    $response->assertSee('value="2026-03-10"', false);
});

it('lets an admin create weekly recurring academy trainings in a selected period', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $group = AcademyGroup::query()->create([
        'name' => 'Kadeci U15M',
        'code' => 'U15M',
        'color' => '#22c55e',
        'is_active' => true,
    ]);

    $this->actingAs($admin)->post(route('admin.academy.trainings.store'), [
        'academy_group_id' => $group->id,
        'title' => 'Trening cykliczny',
        'starts_at' => '2026-03-03T18:00',
        'ends_at' => '2026-03-03T19:30',
        'location' => 'Hala ETB',
        'trainer_name' => 'Jan Trener',
        'status' => AcademyTraining::STATUS_SCHEDULED,
        'repeat_weekly' => '1',
        'repeat_until' => '2026-03-24',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $trainingDates = AcademyTraining::query()
        ->where('title', 'Trening cykliczny')
        ->orderBy('starts_at')
        ->get()
        ->map(fn (AcademyTraining $training) => $training->starts_at->format('Y-m-d H:i'))
        ->all();

    expect($trainingDates)->toBe([
        '2026-03-03 18:00',
        '2026-03-10 18:00',
        '2026-03-17 18:00',
        '2026-03-24 18:00',
    ]);
});

it('lets an admin create academy trainings from separate date and time fields with group trainer suggestions', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $group = AcademyGroup::query()->create([
        'name' => 'Juniorzy U17M',
        'code' => 'U17M',
        'color' => '#3b82f6',
        'is_active' => true,
    ]);

    $group->trainers()->create([
        'name' => 'Jan Trener',
        'role' => 'Trener główny',
        'phone' => '500 100 200',
    ]);

    $panel = $this->actingAs($admin)->get(route('profile.edit', ['section' => 'academy']));
    $panel->assertOk();
    $panel->assertSee('data-academy-training-trainers', false);
    $panel->assertSee('Jan Trener');

    $this->actingAs($admin)->post(route('admin.academy.trainings.store'), [
        'academy_group_id' => $group->id,
        'training_date' => '2026-07-10',
        'start_time' => '18:00',
        'end_time' => '19:30',
        'title' => 'Trening z osobnych pól',
        'location' => 'Hala ETB',
        'trainer_name' => 'Jan Trener',
        'status' => AcademyTraining::STATUS_SCHEDULED,
        'repeat_weekly' => '1',
        'repeat_until' => '2026-07-24',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $dates = AcademyTraining::query()
        ->where('title', 'Trening z osobnych pól')
        ->orderBy('starts_at')
        ->get()
        ->map(fn (AcademyTraining $training) => [
            $training->starts_at->format('Y-m-d H:i'),
            $training->ends_at?->format('Y-m-d H:i'),
            $training->trainer_name,
        ])
        ->all();

    expect($dates)->toBe([
        ['2026-07-10 18:00', '2026-07-10 19:30', 'Jan Trener'],
        ['2026-07-17 18:00', '2026-07-17 19:30', 'Jan Trener'],
        ['2026-07-24 18:00', '2026-07-24 19:30', 'Jan Trener'],
    ]);
});

it('shows global academy calendar notes in the public calendar day preview', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $group = AcademyGroup::query()->create([
        'name' => 'Młodzicy U13',
        'code' => 'U13',
        'color' => '#22c55e',
        'is_active' => true,
    ]);

    AcademyTraining::query()->create([
        'academy_group_id' => $group->id,
        'title' => 'Trening techniczny',
        'starts_at' => '2026-07-10 18:00:00',
        'ends_at' => '2026-07-10 19:30:00',
        'location' => 'Hala ETB',
        'trainer_name' => 'Jan Trener',
        'status' => AcademyTraining::STATUS_SCHEDULED,
    ]);

    $this->actingAs($admin)->post(route('admin.academy.calendar-notes.store'), [
        'title' => 'Zwolnienie z zajęć',
        'body' => 'Hala dostępna dopiero od godziny 17:00.',
        'starts_on' => '2026-07-10',
        'ends_on' => '2026-07-12',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $response = $this->get(route('academy', ['month' => '2026-07-01']));

    $response->assertOk();
    $response->assertSee('Zwolnienie z zajęć');
    $response->assertSee('Hala dostępna dopiero od godziny 17:00.');
    $response->assertSee('Podgląd dnia');
    $response->assertSee('data-academy-day-preview', false);
    $this->assertDatabaseHas('academy_calendar_notes', [
        'title' => 'Zwolnienie z zajęć',
        'starts_on' => '2026-07-10 00:00:00',
        'ends_on' => '2026-07-12 00:00:00',
    ]);
});

it('marks Sundays and synchronized Polish public holidays in the academy calendar', function () {
    config([
        'services.nager_date.enabled' => true,
        'services.nager_date.base_url' => 'https://date.nager.at',
    ]);

    Http::fake([
        'date.nager.at/api/v4/Holidays/PL/2026' => Http::response([
            [
                'date' => '2026-05-03',
                'name' => 'Constitution Day',
                'countryCode' => 'PL',
                'nationalHoliday' => true,
                'holidayTypes' => ['Public'],
            ],
        ]),
    ]);

    $response = $this->get(route('academy', ['month' => '2026-05-01']));

    $response->assertOk();
    $response->assertSee('data-academy-calendar-day="2026-05-03"', false);
    $response->assertSee('data-academy-holiday="1"', false);
    $response->assertSee('bg-red-50', false);
    $response->assertSee('text-red-500', false);
    $response->assertSee('Święto Konstytucji 3 Maja');
    $response->assertDontSee('Constitution Day');
    $response->assertSee('Święta publiczne są synchronizowane z Nager.Date', false);
    $response->assertSee('https://date.nager.at', false);
});

it('uses Polish holiday names even when older English API data is cached', function () {
    config(['services.nager_date.enabled' => true]);

    Cache::put('polish_public_holidays:v2:2026', [
        [
            'date' => '2026-08-15',
            'name' => 'Assumption Day',
        ],
    ], now()->addDay());

    $response = $this->get(route('academy', ['month' => '2026-08-01']));

    $response->assertOk();
    $response->assertSee('Wniebowzięcie Najświętszej Maryi Panny');
    $response->assertDontSee('Assumption Day');
});

it('uses Polish display names from the public holidays reference style', function () {
    config(['services.nager_date.enabled' => true]);

    Cache::put('polish_public_holidays:v2:2026', [
        [
            'date' => '2026-04-06',
            'name' => 'Easter Monday',
        ],
        [
            'date' => '2026-05-24',
            'name' => 'Pentecost',
        ],
    ], now()->addDay());

    $response = $this->get(route('academy', ['month' => '2026-04-01']));

    $response->assertOk();
    $response->assertSee('Drugi Dzień Wielkanocy');
    $response->assertDontSee('Easter Monday');

    $response = $this->get(route('academy', ['month' => '2026-05-01']));

    $response->assertOk();
    $response->assertSee('Zielone Świątki');
    $response->assertDontSee('Pentecost');
});

it('suggests existing academy trainers and reuses their contact number for admins', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $group = AcademyGroup::query()->create([
        'name' => 'Juniorzy U17M',
        'code' => 'U17M',
        'color' => '#3b82f6',
        'is_active' => true,
    ]);

    $group->trainers()->create([
        'name' => 'Łukasz Kowalski',
        'role' => 'Trener główny',
        'email' => 'lukasz@example.com',
        'phone' => '501 222 333',
    ]);

    $response = $this->actingAs($admin)->getJson(route('admin.academy.trainers.suggestions', ['q' => 'łuk']));

    $response->assertOk()
        ->assertJsonFragment([
            'name' => 'Łukasz Kowalski',
            'phone' => '501 222 333',
            'email' => 'lukasz@example.com',
            'role' => 'Trener główny',
        ]);
});

it('shows academy color presets without black white or reserved yellow in admin panel', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $response = $this->actingAs($admin)->get(route('profile.edit', ['section' => 'academy']));

    $response->assertOk();
    expect(substr_count($response->getContent(), 'data-academy-color-preset'))->toBe(10);
    $response->assertDontSee('data-academy-color-preset="#000000"', false);
    $response->assertDontSee('data-academy-color-preset="#ffffff"', false);
    $response->assertDontSee('data-academy-color-preset="#facc15"', false);
    $response->assertSee('Wybierz inny kolor', false);
});

it('rejects black and white academy group colors but allows manual yellow', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)->post(route('admin.academy.groups.store'), [
        'name' => 'Biała grupa',
        'code' => 'WHITE',
        'color' => '#ffffff',
        'is_active' => '1',
    ])->assertSessionHasErrors('color');

    $this->actingAs($admin)->post(route('admin.academy.groups.store'), [
        'name' => 'Seniorzy',
        'code' => 'SEN',
        'color' => '#facc15',
        'is_active' => '1',
    ])->assertRedirect(route('profile.edit', ['section' => 'academy']));

    $this->assertDatabaseHas('academy_groups', [
        'code' => 'SEN',
        'color' => '#facc15',
    ]);
});
