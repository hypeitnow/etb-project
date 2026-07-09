<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyTraining;
use App\Models\User;
use App\Services\AdminNotificationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademyTrainingController extends Controller
{
    public function __construct(private readonly AdminNotificationService $notificationService)
    {
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $recurrence = $this->recurrence($request);
        $trainings = $this->createTrainings($data, $recurrence);
        $training = $trainings[0];
        $this->notificationService->record($request->user(), 'created', $training, "Trening akademii: {$training->group?->code}");

        $message = count($trainings) > 1
            ? 'Cykliczne treningi akademii zostały zapisane.'
            : 'Trening akademii został zapisany.';

        return redirect()->route('profile.edit', ['section' => 'academy'])->with('success', $message);
    }

    public function update(Request $request, AcademyTraining $training): RedirectResponse
    {
        $training->update($this->validated($request));
        $this->notificationService->record($request->user(), 'updated', $training, "Trening akademii: {$training->group?->code}");

        return redirect()->route('profile.edit', ['section' => 'academy'])->with('success', 'Trening akademii został zaktualizowany.');
    }

    public function cancel(Request $request, AcademyTraining $training): RedirectResponse
    {
        $data = $request->validate([
            'cancelled_reason' => ['required', 'string', 'max:3000'],
        ]);

        $training->update([
            'status' => AcademyTraining::STATUS_CANCELLED,
            'cancelled_reason' => $data['cancelled_reason'],
        ]);
        $this->notificationService->record($request->user(), 'updated', $training, "Odwołany trening: {$training->group?->code}");

        return redirect()->route('profile.edit', ['section' => 'academy'])->with('success', 'Trening został odwołany.');
    }

    public function restore(AcademyTraining $training): RedirectResponse
    {
        $training->update([
            'status' => AcademyTraining::STATUS_SCHEDULED,
            'cancelled_reason' => null,
        ]);
        $this->notificationService->record(request()->user(), 'updated', $training, "Przywrócony trening: {$training->group?->code}");

        return redirect()->route('profile.edit', ['section' => 'academy'])->with('success', 'Trening został przywrócony.');
    }

    public function destroy(AcademyTraining $training): RedirectResponse
    {
        abort_unless(request()->user()?->role === User::ROLE_ADMIN, 403);

        $label = "Trening akademii: {$training->group?->code} {$training->starts_at?->format('d.m.Y H:i')}";
        $id = $training->id;
        $training->delete();
        $this->notificationService->recordDeleted(request()->user(), AcademyTraining::class, $id, $label);

        return redirect()->route('profile.edit', ['section' => 'academy'])->with('success', 'Trening został usunięty.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'academy_group_id' => ['required', 'exists:academy_groups,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'training_date' => ['nullable', 'required_without:starts_at', 'date'],
            'start_time' => ['nullable', 'required_with:training_date', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'starts_at' => ['nullable', 'required_without:training_date', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'trainer_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in([AcademyTraining::STATUS_SCHEDULED, AcademyTraining::STATUS_CANCELLED])],
            'cancelled_reason' => ['nullable', 'required_if:status,'.AcademyTraining::STATUS_CANCELLED, 'string', 'max:3000'],
        ]);

        if ($data['status'] !== AcademyTraining::STATUS_CANCELLED) {
            $data['cancelled_reason'] = null;
        }

        if (filled($data['training_date'] ?? null)) {
            $data['starts_at'] = "{$data['training_date']} {$data['start_time']}";
            $data['ends_at'] = filled($data['end_time'] ?? null) ? "{$data['training_date']} {$data['end_time']}" : null;
        }

        unset($data['training_date'], $data['start_time'], $data['end_time']);

        return $data;
    }

    /**
     * @return array{repeat_weekly: bool, repeat_until: string|null}
     */
    private function recurrence(Request $request): array
    {
        $dateField = $request->filled('training_date') ? 'training_date' : 'starts_at';
        $data = $request->validate([
            'repeat_weekly' => ['nullable', 'boolean'],
            'repeat_until' => ['nullable', 'required_if:repeat_weekly,1', 'date', "after_or_equal:{$dateField}"],
        ]);

        return [
            'repeat_weekly' => $request->boolean('repeat_weekly'),
            'repeat_until' => $data['repeat_until'] ?? null,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @param array{repeat_weekly: bool, repeat_until: string|null} $recurrence
     * @return array<int, AcademyTraining>
     */
    private function createTrainings(array $data, array $recurrence): array
    {
        $start = CarbonImmutable::parse($data['starts_at']);
        $end = filled($data['ends_at'] ?? null) ? CarbonImmutable::parse($data['ends_at']) : null;
        $repeatUntil = $recurrence['repeat_weekly'] && $recurrence['repeat_until']
            ? CarbonImmutable::parse($recurrence['repeat_until'])->endOfDay()
            : $start;

        $trainings = [];
        $occurrenceStart = $start;
        $occurrenceEnd = $end;

        while ($occurrenceStart->lessThanOrEqualTo($repeatUntil)) {
            $trainings[] = AcademyTraining::query()->create([
                ...$data,
                'starts_at' => $occurrenceStart,
                'ends_at' => $occurrenceEnd,
            ]);

            if (! $recurrence['repeat_weekly']) {
                break;
            }

            $occurrenceStart = $occurrenceStart->addWeek();
            $occurrenceEnd = $occurrenceEnd?->addWeek();
        }

        return $trainings;
    }
}
