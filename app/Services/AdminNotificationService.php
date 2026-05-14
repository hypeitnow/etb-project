<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationService
{
    public function record(?User $actor, string $action, Model $subject, string $label, ?string $description = null): AdminNotification
    {
        return AdminNotification::query()->create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'subject_label' => $label,
            'description' => $description ?? $this->description($actor, $action, $label),
            'payload' => [
                'model' => class_basename($subject),
                'label' => $label,
            ],
        ]);
    }

    public function recordDeleted(?User $actor, string $subjectClass, int|string|null $subjectId, string $label): AdminNotification
    {
        return AdminNotification::query()->create([
            'actor_id' => $actor?->id,
            'action' => 'deleted',
            'subject_type' => $subjectClass,
            'subject_id' => $subjectId,
            'subject_label' => $label,
            'description' => $this->description($actor, 'deleted', $label),
            'payload' => [
                'model' => class_basename($subjectClass),
                'label' => $label,
            ],
        ]);
    }

    private function description(?User $actor, string $action, string $label): string
    {
        $name = $actor?->name ?? 'System';
        $verb = match ($action) {
            'created' => 'dodał',
            'updated' => 'zaktualizował',
            'deleted' => 'usunął',
            'published' => 'opublikował',
            default => 'zmienił',
        };

        return "{$name} {$verb}: {$label}";
    }
}
