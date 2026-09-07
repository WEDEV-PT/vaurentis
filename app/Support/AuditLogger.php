<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /** @param array<string, mixed> $details */
    public static function record(string $action, Model $target, array $details = []): void
    {
        if (app()->runningInConsole() || ! auth()->check()) {
            return;
        }

        self::write($action, $target, auth()->id(), $details);
    }

    /** @param array<string, mixed> $details */
    public static function recordAnonymous(string $action, Model $target, array $details = []): void
    {
        self::write($action, $target, null, $details);
    }

    /** @param array<string, mixed> $details */
    private static function write(string $action, Model $target, ?int $actorId, array $details): void
    {
        $request = request();

        AuditLog::query()->create([
            'actor_id' => $actorId,
            'project_id' => $target instanceof Project ? $target->id : ($details['project_id'] ?? null),
            'action' => $action,
            'target_type' => class_basename($target),
            'target_id' => $target->getKey(),
            'target_label' => $target->getAttribute('name'),
            'details' => $details ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'occurred_at' => now('UTC'),
        ]);
    }

    /** @param array<string, mixed> $changes */
    public static function recordUpdate(string $action, Model $target, array $changes): void
    {
        $fields = array_values(array_diff(array_keys($changes), [
            'updated_at',
            'password',
            'remember_token',
            'html_content',
            'slug',
        ]));

        if ($fields === []) {
            return;
        }

        self::record($action, $target, ['changed_fields' => $fields]);
    }
}
